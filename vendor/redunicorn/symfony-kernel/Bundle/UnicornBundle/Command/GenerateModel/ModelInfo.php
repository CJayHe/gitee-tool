<?php

namespace RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Command\GenerateModel;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\JoinColumn;
use RedUnicorn\SymfonyKernel\Exception\UnicornException;

class ModelInfo
{
    /**
     * @var AnnotationReader
     */
    private $reader;

    /**
     * @var \ReflectionClass
     */
    private $reflClass;

    private $entity_class;

    private $bundle_name;

    private $bundle_namespace;

    private $bundle_file_path;

    private $entity_class_name;

    private $table_name;

    private $primary_key_field_name;

    private $primary_key_generated_value_strategy;

    /**
     * @var array
     */
    private $eath;

    public function __construct($entity_class)
    {
        $entity_class = ltrim($entity_class, '\\');
        $this->reader = new AnnotationReader();
        $this->reflClass = new \ReflectionClass($entity_class);
        $annotation_table = $this->reader->getClassAnnotation($this->reflClass, 'Doctrine\ORM\Mapping\Table');

        if(empty($annotation_table)){
            throw new UnicornException('未定义结构');
        }

        $this->table_name = $annotation_table->name;


        $this->entity_class = $entity_class;
        $entity_class_info = explode('\\' , $entity_class);
        if(count($entity_class_info) == 2){
            $this->bundle_name = $entity_class_info[0];
            $this->bundle_namespace = $entity_class_info[0];
        }else{
            $this->bundle_name = $entity_class_info[count($entity_class_info) - 1];
            $this->bundle_namespace = $entity_class_info[0];
            for ($i = 1; $i < (count($entity_class_info) - 2); $i++){
                $this->bundle_namespace .= '\\' . $entity_class_info[$i];
            }
        }

        $this->entity_class_name = end($entity_class_info);

        $entity_path_info = explode('/', $this->reflClass->getFileName());
        $this->bundle_file_path = $entity_path_info[0];
        for ($i = 1; $i < (count($entity_path_info) - 2); $i++) {
            $this->bundle_file_path .= '/' . $entity_path_info[$i];
        }

        $this->eath();
    }

    public function getEntityFileName()
    {
        return $this->entity_class_name . '.php';
    }

    public function getModelFileName()
    {
        return 'm_' . strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $this->getEntityFileName()));
    }

    public function getModelFilePath()
    {
        return $this->bundle_file_path . '/Model/' . $this->getModelFileName();
    }

    public function hasModelFile()
    {
        return is_file($this->getModelFilePath());
    }

    public function getModelClassName()
    {
        return basename($this->getModelFilePath(), '.php');
    }

    public function getEntityClassName()
    {
        return $this->entity_class_name;
    }

    public function getEntityNamespace()
    {
        return $this->reflClass->getNamespaceName();
    }

    public function getModelNamespace()
    {

        return $this->bundle_namespace . '\\' . 'Model';
    }

    public function getTableName()
    {
        return $this->table_name;
    }

    public function getEntityClass()
    {
        return $this->entity_class;
    }

    public function getModelClass()
    {
        return '\\' . $this->getModelNamespace() . '\\' . $this->getModelClassName();
    }

    public function getTableNameAlias()
    {
        $alias = '';
        foreach (explode('_', $this->table_name) as $str){
            if(!empty($str)) {
                $alias .= substr($str, 0, 1);
            }
        }

        return $alias;
    }

    public function getPrimaryKeyFieldName()
    {
        return $this->primary_key_field_name;
    }

    public function getPrimaryKeyGeneratedValueStrategy()
    {
        return $this->primary_key_generated_value_strategy;
    }

    private function getRootPath()
    {
        return  dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));
    }

    private function eath()
    {
        $properties = $this->reflClass->getProperties();

        foreach ($properties as $property) {
            $column = $this->reader->getPropertyAnnotation($property, 'Doctrine\ORM\Mapping\Column');
            $joinColumn = $this->reader->getPropertyAnnotation($property, 'Doctrine\ORM\Mapping\JoinColumn');

            if(empty($column) && empty($joinColumn)){
                continue;
            }

            $column = $this->reader->getPropertyAnnotation($property, 'Doctrine\ORM\Mapping\Column');
            $joinColumn = $this->reader->getPropertyAnnotation($property, 'Doctrine\ORM\Mapping\JoinColumn');

            if(empty($column) && empty($joinColumn)){
                continue;
            }

            $arr = array();

            $arr['name'] =  empty($column) ? $joinColumn->name : $column->name ;
            $arr['isNull'] = empty($column) ? $joinColumn->nullable : $column->nullable;
            $arr['unique'] = empty($column) ? $joinColumn->unique : $column->unique;

            //主键识别区
            if(empty($this->getPrimaryKeyFieldName())){
                $arr['isPrimaryKey'] =  empty($this->reader->getPropertyAnnotation($property, 'Doctrine\ORM\Mapping\Id')) ? false : true;
            }else{
                $arr['isPrimaryKey'] = false;
            }

            if($arr['isPrimaryKey']){
                $this->primary_key_field_name = $arr['name'];
            }

            if($arr['isPrimaryKey']) {
                $generatedValue = $this->reader->getPropertyAnnotation($property, 'Doctrine\ORM\Mapping\GeneratedValue');
                if(!empty($generatedValue)) {
                    $this->primary_key_generated_value_strategy = $generatedValue;
                }
            }

            //外键识别区
            $arr['isJoinKey'] = empty($joinColumn) ? false : true;

            if($arr['isJoinKey']){
                $join = $this->reader->getPropertyAnnotation($property, 'Doctrine\ORM\Mapping\OneToOne');
                if(empty($join)){
                    $join = $this->reader->getPropertyAnnotation($property, 'Doctrine\ORM\Mapping\ManyToOne');
                    if (empty($join)){
                        throw new UnicornException('不支持多对多外键代码识别');
                    }
                    $arr['join'] = 'ManyToOne';
                }else{
                    $arr['join'] = 'OneToOne';
                }

                $arr['targetEntity'] = $this->getTargetEntity($join->targetEntity);
                $arr['referencedColumnName'] = $joinColumn->referencedColumnName;
            }


            //基础识别区
            $arr['type'] = $arr['isJoinKey'] ? 'class' : $column->type;

            $this->eath[$arr['name']] = ['property_name' => $property->getName()] + $arr;
        }

        if(empty($this->getPrimaryKeyFieldName())){
            throw new UnicornException('必须存在主键');
        }
    }

    public function getFieldInfo($field)
    {
        return $this->eath[$field];
    }

    public function hasField($field)
    {
        return array_key_exists($field, $this->eath);
    }

    public function getEach()
    {
        return $this->eath;
    }

    private function getTargetEntity($targetEntity)
    {
        if(substr($targetEntity, 0, 1) != '\\'){
            if(strpos($targetEntity,'\\') !== false){
                $targetEntity = '\\' . $targetEntity;
            }else{
                $targetEntity = '\\' .  $this->getEntityNamespace()  . '\\' . $targetEntity;
            }
        }

        return $targetEntity;
    }

    public function to_array()
    {
        return array(
            'entity_class_name' => $this->getEntityClassName(),
            'table_name' => $this->getTableName(),
            'table_name_alias' => $this->getTableNameAlias()
        );
    }
}