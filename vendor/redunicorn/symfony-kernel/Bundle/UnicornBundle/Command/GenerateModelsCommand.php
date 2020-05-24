<?php
/**
 * PhpStorm.
 * User: Jay
 * Date: 2018/5/2
 */

namespace RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Command;

use RedUnicorn\SymfonyKernel\ClassGeneration\Argument;
use ClassGeneration\DocBlock;
use ClassGeneration\NamespaceClass;
use ClassGeneration\Property;
use ClassGeneration\UseClass;
use ClassGeneration\UseCollection;
use ClassGeneration\Writer;
use Doctrine\Common\Annotations\AnnotationReader;
use RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Command\GenerateModel\ModelCode;
use RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Command\GenerateModel\ModelInfo;
use RedUnicorn\SymfonyKernel\ClassGeneration\Method;
use RedUnicorn\SymfonyKernel\ClassGeneration\PhpClass;
use RedUnicorn\SymfonyKernel\ClassGeneration\Tool;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateModelsCommand extends Base
{
    private $path = 'src';

    public function configure()
    {
        $this
            ->setName($this->command_pre . 'generate:models')
            ->setDescription($this->description_pre . '生成Model层代码')
            ->addOption('entity' , null, InputOption::VALUE_OPTIONAL, <<<EOF
<info>指定需要生成的Entity</info>            
<comment>--entity=Base</comment> or <comment>--entity=Base:Admin</comment>
EOF
, 'Base')
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, <<<EOF
<info>指定需要生成的Bundle路径</info>
<comment>--path=vendor/redunicorn/annex/AnnexBundle</comment>
EOF
, 'src')
            ->setHelp(<<<EOF
<info>%command.name%</info> 基于Entity生成Model层初始代码
<comment>注意：已生成的文件将不会再次生成</comment>

<info>生成src目录下BaseBanner的model层代码</info>
    <comment>php app/console  unicorn:generate:models</comment>
<info>生成src目录下指定Banner的model层代码</info>    
    <comment>php app/console  unicorn:generate:models --entity=Base</comment>
    <comment>php app/console  unicorn:generate:models --entity=BaseBundle</comment>
<info>生层src目录下指定Banner指定Entity的代码</info>    
    <comment>php app/console  unicorn:generate:models --entity=Base:Admin</comment>
    <comment>php app/console  unicorn:generate:models --entity=BaseBundle:Admin</comment>
<info>生层venner目录下指定Banner的代码</info>
    <comment>php app/console  unicorn:generate:models --entity=RedUnicorn\\Annex\\AnnexBundle --path=vendor/redunicorn/annex/AnnexBundle</comment>
EOF
            );
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io->title($this->getName());

        $entity = $input->getOption('entity');
        $path = $input->getOption('path');

        $e =  explode(':', $entity);

        if(count($e) != 2 && count($e) != 1){
            $this->io->error('--entity 输入不正确 请用--help得到帮助');
        }

        if(strpos($e[0], 'Bundle') === false) {
            $bundle = $e[0] . 'Bundle';
        }else{
            $bundle = $e[0];
        }

        if(empty($path) || $path == 'src') {
            $path = 'src/' . $bundle ;
        }else{
            $this->path = 'vendor';
        }

        if (!is_dir($this->getRootPath() . '/' . $path)) {
            $this->io->error($bundle . '不存在');
            return;
        }

        $entity_path = $this->getRootPath() . '/' . $path . '/Entity';

        if (!is_dir($entity_path)) {
            $this->io->error($entity_path . '目录不存在');
            return;
        }

        $entitys = [];

        if(count($e) == 2) {
            $entity = $e[1];
            if(empty($entity)) {
                $this->io->error('--entity 输入不正确 请用--help得到帮助');
                return;
            }

            $entity_file_path = $entity_path . '/' . $entity . '.php';

            if(!is_file($entity_file_path)){
                $this->io->error('entity path ' . $entity_file_path . ' 不存在');
                return;
            }

            $entitys[] = $entity . '.php';

        } else {
            $entitys = scandir($entity_path);

            foreach ($entitys as $index => $value){
                if(strpos($value,'.php') === false){
                    unset($entitys[$index]);
                }elseif(in_array($value , array(
                    'Snapshot.php'
                ))){
                    unset($entitys[$index]);
                }
            }

            if(empty($entitys)){
                $this->io->error('操作失败,' . $entity_path . '无可生成文件');
                return;
            }
        }

        $success_num = 0;
        $error_num = 0;

        foreach ($entitys as $filename){
            if($this->generateModel($bundle . '\\' . 'Entity' . '\\' . basename($filename, '.php'))){
                $success_num ++;
            }else{
                $error_num ++;
            }
        }

        $this->io->success('执行结束 success:' . $success_num . '; error:' . $error_num . '; count:' . count($entitys));
    }

    /**
     * 生成model
     *
     * @param $entity_class
     * @return bool
     */
    public function generateModel($entity_class)
    {
        try {
            $model_info = new ModelInfo($entity_class);
        }catch (\Exception $exception){
            $this->io->error($exception->getMessage());
            return false;
        }

        if ($model_info->hasModelFile()) {
            $this->io->error($entity_class . '已存在');
            return false;
        }

        $model_info_code = new ModelCode($model_info);

        $model_code = new PhpClass();
        $tool = new Tool();

        $model_code->setNamespace(new NamespaceClass($model_info->getModelNamespace()));

        $uses = [
            'Doctrine\ORM\Mapping\ClassMetadata' => '',
            'RedUnicorn\SymfonyKernel\Model\Model' => '',
            'Symfony\Component\DependencyInjection\ContainerInterface' => '',
            'RedUnicorn\Validate\Validate' => '',
            $model_info->getEntityClass() => '',
        ];

        foreach ($uses as $class_name => $alias) {
            $use = new UseClass();
            $use->setClassName($class_name)->setAlias($alias);
            $use->setAlias($alias);
            $model_code->addUse($use);
        }

        $model_code->setName($model_info->getModelClassName());
        $model_code->setExtends('Model');

        $property = new Property();
        $property->setName($model_info->getTableName() . '_class');
        $property->setDescription('@var ' . $model_info->getEntityClassName());
        $model_code->addProperty($property);

        //__construct
        $method = new Method();
        $method->setName('__construct');
        $argument = new Argument();
        $argument->setName('container');
        $argument->setValue(null);
        $argument->setType('ContainerInterface');
        $method->addArgument($argument);

        if ($model_info->getPrimaryKeyFieldName() != 'id') {
            $method->setCode(vsprintf('$this->tel_sql_array["primary_key"] = "%s";', $model_info->getPrimaryKeyFieldName()));
            $method->setCode(vsprintf('$this->tel_sql_array["final_order_by"] = "%s";', '.' . $model_info->getPrimaryKeyFieldName() . ' DESC'));
        }

        $method->setCode('parent::__construct($container, "' . $model_info->getTableName() . '");');
        $model_code->addMethod($method);

        //validate
        $method = new Method();
        $method->setName('validate');
        $argument = new Argument();
        $argument->setName('data');
        $method->addArgument($argument);
        $argument = new Argument();
        $argument->setName('id');
        $argument->setValue(false);
        $method->addArgument($argument);

        $method->setCode($tool->getCodeToTemplate(__DIR__ . '/GenerateModel/CodeTemplate/function_validate.tel', $model_info_code->to_array()));
        $model_code->addMethod($method);

        //insert
        $method = new Method();
        $method->setName('insert');
        $argument = new Argument();
        $argument->setName('data');
        $method->addArgument($argument);

        $method->setCode($tool->getCodeToTemplate(__DIR__ . '/GenerateModel/CodeTemplate/function_insert.tel', $model_info_code->to_array()));
        $model_code->addMethod($method);

        //update
        $method = new Method();
        $method->setName('update');
        $argument = new Argument();
        $argument->setName('data');
        $method->addArgument($argument);
        $argument = new Argument();
        $argument->setName('id');
        $method->addArgument($argument);

        $method->setCode($tool->getCodeToTemplate(__DIR__ . '/GenerateModel/CodeTemplate/function_update.tel', $model_info_code->to_array()));
        $model_code->addMethod($method);


        //rewritesRule
        $method = new Method();
        $method->setName('rewritesRule');
        $argument = new Argument();
        $argument->setName('rules');
        $argument->setIsStatus(true);
        $method->addArgument($argument);

        $method->setCode($model_info_code->__get('rule_code'));
        $model_code->addMethod($method);

        //rewriteInfo
        if(!empty($model_info_code->__get('rewriteInfoCode'))){
            $method = new Method();
            $method->setName('rewriteInfo');
            $argument = new Argument();
            $argument->setName('info');
            $argument->setIsStatus(true);
            $method->addArgument($argument);

            $method->setCode($model_info_code->__get('rewriteInfoCode'));
            $model_code->addMethod($method);
        }

        //joinInfo
        $method = new Method();
        $method->setName('joinInfo');
        $method->setIsStatic(true);
        $argument = new Argument();
        $argument->setName('model');
        $argument->setType('Model');
        $method->addArgument($argument);
        $argument = new Argument();
        $argument->setName('rules');
        $argument->setIsStatus(true);
        $method->addArgument($argument);
        $argument = new Argument();
        $argument->setName('joinColumnName');
        $argument->setValue(null);
        $method->addArgument($argument);
        $argument = new Argument();
        $argument->setName('referencedColumnName');
        $argument->setValue(null);
        $method->addArgument($argument);

        $method->setCode($tool->getCodeToTemplate(__DIR__ . '/GenerateModel/CodeTemplate/funtion_join_info.tel', $model_info->to_array()));
        $model_code->addMethod($method);

        //getFieldName
        $method = new Method();
        $method->setName('getFieldName');
        $method->setIsStatic(true);
        $argument = new Argument();
        $argument->setName('model');
        $argument->setType('Model');
        $method->addArgument($argument);
        $argument = new Argument();
        $argument->setName('rules');
        $method->addArgument($argument);

        $method->setCode(vsprintf('return "%s";', array($model_info->getTableName() . '_id')));
        $model_code->addMethod($method);


        //rewriteDelete
        if ($model_info->hasField('is_del')) {
            $method = new Method();
            $method->setName('rewriteDelete');
            $argument = new Argument();
            $argument->setName('id');
            $method->addArgument($argument);

            $method->setCode($tool->getCodeToTemplate(__DIR__ . '/GenerateModel/CodeTemplate/function_del.tel', $model_info->to_array()));
            $model_code->addMethod($method);
        }

        $w = new Writer();
        $w->setPhpClass($model_code)->setPath($this->getRootPath() . '/'. $this->path)->write();

        $this->io->success($entity_class . '生成成功');
        return true;

    }
}