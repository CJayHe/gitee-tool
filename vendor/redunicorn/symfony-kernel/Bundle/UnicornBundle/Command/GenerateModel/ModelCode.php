<?php
namespace RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Command\GenerateModel;

use RedUnicorn\SymfonyKernel\ClassGeneration\Tool;
use RedUnicorn\SymfonyKernel\Exception\UnicornException;
use Symfony\Component\Intl\ResourceBundle\LanguageBundleInterface;

class ModelCode
{
    private $model_info;

    private $tool;

    private $rule_code;

    private $validate_code;

    private $other_before_code;

    private $other_alter_code;

    private $insert_alter_code;

    private $update_alter_code;

    private $primary_key_funtion_name;

    private $join_info_code;

    private $rewriteInfoCode;

    public function __construct(ModelInfo $modelInfo)
    {
        $this->model_info = $modelInfo;
        $this->tool = new Tool();

        $this->eath();
    }

    private function eath()
    {
        foreach ($this->model_info->getEach() as $key => $field)
        {
            if($field['isPrimaryKey']){
                $this->primary_key_funtion_name = $this->getFieldFuntionName($field['property_name']);
            }

            //validate_code
            if(!($field['isPrimaryKey'] && $this->model_info->getPrimaryKeyGeneratedValueStrategy() != 'NONE')) {

                switch ($field['name']){
                    case 'create_at':
                        $this->append('insert_alter_code', vsprintf('$this->%s_class->set%s(new \DateTime()); ', [$this->model_info->getTableName(), $this->getFieldFuntionName($field['property_name'])]));
                        break;
                    case 'update_at':
                        $this->append('update_alter_code', vsprintf('$this->%s_class->set%s(new \DateTime()); ', [$this->model_info->getTableName(), $this->getFieldFuntionName($field['property_name'])]));
                        break;
                    default:
                        if($field['isJoinKey']){
                            $this->append('validate_code', $this->tool->getCodeToTemplate(__DIR__ . '/CodeTemplate/validate_join_code_tel',
                                array_merge($this->model_info->to_array(), array(
                                    'column_name' => $field['name'],
                                    'funtion_name' => $this->getFieldFuntionName($field['property_name']),
                                    'targetEntity' => $field['targetEntity']
                                ))));
                        }else {
                            $this->append('validate_code', $this->tool->getCodeToTemplate(__DIR__ . '/CodeTemplate/validate_code.tel',
                                array_merge($this->model_info->to_array(), array(
                                    'column_name' => $field['name'],
                                    'funtion_name' => $this->getFieldFuntionName($field['property_name'])
                                ))));
                        }
                }
            }

            $join_model_class = [];

            //rule_code
            switch ($field['type']){
                case 'boolean':
                    switch ($field['name']){
                        case 'is_del':
                            $this->append('rule_code' , $this->tool->getCodeToTemplate(__DIR__ . '/CodeTemplate/rule_code_else.tel',
                                array_merge($this->model_info->to_array(), array(
                                    'rule_name' => $field['name'],
                                    'rule_where' => vsprintf(' && !Validate::isRealEmpty($rules["%s"]) ', [$field['name']]),
                                    'rule_code' => vsprintf('$this->sql_array["where"] .= " AND sql_pre.%s = ' . "'" . '{$rules["%s"]}'. "'" .'";', [$field['name'], $field['name']]),
                                    'else_rule_code' => vsprintf('$this->sql_array["where"] .= " AND sql_pre.%s = ' . "'" . '%s'. "'" .'";', [$field['name'], '0']),
                                    'annotation' => 'where'
                                ))));
                            break;
                        default:
                            $this->append('rule_code' , $this->tool->getCodeToTemplate(__DIR__ . '/CodeTemplate/rule_code.tel',
                                array_merge($this->model_info->to_array(), array(
                                    'rule_name' => $field['name'],
                                    'rule_where' => vsprintf(' && !Validate::isRealEmpty($rules["%s"]) ', [$field['name']]),
                                    'rule_code' => vsprintf('$this->sql_array["where"] .= " AND sql_pre.%s = ' . "'" . '{$rules["%s"]}'. "'" .'";', [$field['name'], $field['name']]),
                                    'annotation' => 'where'
                                ))));
                    }
                    break;
                case 'smallint':
                    $this->append('rule_code' , $this->tool->getCodeToTemplate(__DIR__ . '/CodeTemplate/rule_code.tel',
                        array_merge($this->model_info->to_array(), array(
                            'rule_name' => $field['name'],
                            'rule_where' => vsprintf(' && !empty($rules["%s"]) ', [$field['name']]),
                            'rule_code' => vsprintf('$this->sql_array["where"] .= " AND sql_pre.%s in ({$rules["%s"]})";', [$field['name'], $field['name']]),
                            'annotation' => 'where'
                        ))));
                    break;
                case 'integer':
                case 'decimal':
                    $this->append('rule_code' , $this->tool->getCodeToTemplate(__DIR__ . '/CodeTemplate/rule_code.tel',
                        array_merge($this->model_info->to_array(), array(
                            'rule_name' => $field['name'],
                            'rule_where' => vsprintf(' && !empty($rules["%s"]) ', [$field['name']]),
                            'rule_code' => vsprintf('$this->sql_array["where"] .= " AND sql_pre.%s = ' . "'" .'{$rules["%s"]}'. "'" .'";', [$field['name'], $field['name']]),
                            'annotation' => 'where'
                        ))));
                    break;
                case 'datetime':
                case 'date':
                case 'time':
                    $this->append('rule_code' , $this->tool->getCodeToTemplate(__DIR__ . '/CodeTemplate/rule_code.tel',
                        array_merge($this->model_info->to_array(), array(
                            'rule_name' => $field['name'] . '_start',
                            'rule_where' => vsprintf(' && !Validate::isRealEmpty($rules["%s"]) ', [$field['name'] . '_start']),
                            'rule_code' => vsprintf('$this->sql_array["where"] .= " AND UNIX_TIMESTAMP(sql_pre.%s) >= UNIX_TIMESTAMP('. "'" . '{$rules["%s"]}'. "'" .')";', [$field['name'], $field['name'] . '_start']),
                            'annotation' => 'where'
                        ))));
                    $this->append('rule_code' , $this->tool->getCodeToTemplate(__DIR__ . '/CodeTemplate/rule_code.tel',
                        array_merge($this->model_info->to_array(), array(
                            'rule_name' => $field['name'] . '_end',
                            'rule_where' => vsprintf(' && !Validate::isRealEmpty($rules["%s"]) ', [$field['name'] . '_end']),
                            'rule_code' => vsprintf('$this->sql_array["where"] .= " AND UNIX_TIMESTAMP(sql_pre.%s) <= UNIX_TIMESTAMP('. "'" . '{$rules["%s"]}'. "'" .')";', [$field['name'], $field['name'] . '_end']),
                            'annotation' => 'where'
                        ))));
                    $this->append('rule_code' , $this->tool->getCodeToTemplate(__DIR__ . '/CodeTemplate/rule_code.tel',
                        array_merge($this->model_info->to_array(), array(
                            'rule_name' => $field['name'] . '_like',
                            'rule_where' => vsprintf(' && !Validate::isRealEmpty($rules["%s"]) ', [$field['name'] . '_like']),
                            'rule_code' => vsprintf('$this->sql_array["where"] .= " AND sql_pre.%s LIKE ' . "'" . '{$rules["%s"]}'. "%s'" .'";', [$field['name'], $field['name'] . '_like' ,'%']),
                            'annotation' => 'where'
                        ))));
                case 'string':
                    $this->append('rule_code' , $this->tool->getCodeToTemplate(__DIR__ . '/CodeTemplate/rule_code.tel',
                        array_merge($this->model_info->to_array(), array(
                            'rule_name' => $field['name'],
                            'rule_where' => vsprintf(' && !Validate::isRealEmpty($rules["%s"]) ', [$field['name']]),
                            'rule_code' => vsprintf('$this->sql_array["where"] .= " AND sql_pre.%s = ' . "'" . '{$rules["%s"]}'. "'" .'";', [$field['name'], $field['name']]),
                            'annotation' => 'where'
                        ))));

                    if($field['type'] == 'string') {
                        $this->append('rule_code' , $this->tool->getCodeToTemplate(__DIR__ . '/CodeTemplate/rule_code.tel',
                            array_merge($this->model_info->to_array(), array(
                                'rule_name' => $field['name'] . '_like',
                                'rule_where' => vsprintf(' && !Validate::isRealEmpty($rules["%s"]) ', [$field['name'] . '_like']),
                                'rule_code' => vsprintf('$this->sql_array["where"] .= " AND sql_pre.%s LIKE ' . "'%s" . '{$rules["%s"]}'. "%s'" .'";', [$field['name'], '%', $field['name'] . '_like' ,'%']),
                                'annotation' => 'where'
                            ))));
                    }
                    break;
                case  'class':
                    $this->append('rule_code' ,$this->tool->getCodeToTemplate(__DIR__ . '/CodeTemplate/rule_code.tel',
                        array_merge($this->model_info->to_array(), array(
                            'rule_name' => $field['name'],
                            'rule_where' => vsprintf(' && !empty($rules["%s"]) ', [$field['name']]),
                            'rule_code' => vsprintf('$this->sql_array["where"] .= " AND sql_pre.%s = ' . "'" . '{$rules["%s"]}'. "'" .'";', [$field['name'], $field['name']]),
                            'annotation' => 'where'
                        ))));

                    $targetEntityModleInfo = new ModelInfo($field['targetEntity']);

                    $rules_name = 'join_' . $targetEntityModleInfo->getTableName() . '_to_' . $field['name'];

                    $this->append('rule_code' , $this->tool->getCodeToTemplate(__DIR__ . '/CodeTemplate/rule_code.tel',
                        array_merge($this->model_info->to_array(), array(
                            'rule_name' => $rules_name,
                            'rule_where' => '',
                            'rule_code' => vsprintf('%s::joinInfo($this, $rules, "%s", "%s");', [$targetEntityModleInfo->getModelClass(), $field['name'], $field['referencedColumnName']]),
                            'annotation' => 'join'
                        ))));

                    $join_model_class[$targetEntityModleInfo->getModelClass()] = null;
            }

            //rewriteInfoCode
            foreach ($join_model_class as $class => $null) {
                $this->append('rewriteInfoCode', vsprintf('%s::setJoinInfo($this, $info);', array($class)));
            }
        }
    }

    private function getTel($code_template)
    {
        return __DIR__ . '/CodeTemplate/' . $code_template;
    }

    private function getFieldFuntionName($property_name)
    {
        $funtion_name = '';

        foreach (explode('_', $property_name) as $value){
            $funtion_name .= ucfirst($value);
        }

        return $funtion_name;
    }

    private function append($name, $value)
    {
        $this->$name .= $value . PHP_EOL. PHP_EOL;
    }

    public function __get($name)
    {
        return rtrim(rtrim($this->$name));
    }

    public function to_array()
    {
        return $this->model_info->to_array() + array(
                'rule_code' => $this->__get('rule_code'),
                'validate_code' => $this->__get('validate_code'),
                'insert_alter_code' => $this->__get('insert_alter_code'),
                'update_alter_code' => $this->__get('update_alter_code'),
                'other_alter_code' => $this->__get('other_alter_code'),
                'other_alter_code' => $this->__get('other_before_code'),
                'primary_key_funtion_name' => $this->__get('primary_key_funtion_name'),
                'join_info_code' => $this->__get('join_info_code'),
                'rewriteInfoCode' => $this->__get('rewriteInfoCode'),
            );
    }

}