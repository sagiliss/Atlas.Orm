<?php
namespace Atlas\Mapper;

use ArrayIterator;
use Atlas\Relationship\ManyToMany;
use Atlas\Relationship\ManyToOne;
use Atlas\Relationship\OneToMany;
use Atlas\Relationship\OneToOne;

class Relations
{
    protected $relations = [];

    protected $emptyFields = [];

    protected $nativeMapper;

    public function __construct(Mapper $nativeMapper)
    {
        $this->nativeMapper = $nativeMapper;
    }

    public function getEmptyFields()
    {
        return $this->emptyFields;
    }

    public function oneToOne(
        $name,
        $foreignMapperClass,
        callable $custom = null
    ) {
        return $this->set(
            $name,
            OneToOne::CLASS,
            $foreignMapperClass,
            null,
            $custom
        );
    }

    public function oneToMany(
        $name,
        $foreignMapperClass,
        callable $custom = null
    ) {
        return $this->set(
            $name,
            OneToMany::CLASS,
            $foreignMapperClass,
            null,
            $custom
        );
    }

    public function manyToOne(
        $name,
        $foreignMapperClass,
        callable $custom = null
    ) {
        $this->set(
            $name,
            ManyToOne::CLASS,
            $foreignMapperClass,
            null,
            $custom
        );
    }

    public function manyToMany(
        $name,
        $foreignMapperClass,
        $joinField,
        callable $custom = null
    ) {
        return $this->set(
            $name,
            ManyToMany::CLASS,
            $foreignMapperClass,
            $joinField,
            $custom
        );
    }

    public function set(
        $name,
        $relationClass,
        $foreignMapperClass,
        $joinField,
        callable $custom = null
    ) {
        $relation = new $relationClass(
            $this,
            $name,
            $foreignMapperClass,
            $joinField
        );

        if ($custom) {
            $custom($relation);
        }

        $this->relations[$name] = $relation;
        $this->emptyFields[$name] = null;
    }

    public function stitchIntoRecord(Atlas $atlas, Record $record, array $with)
    {
        foreach ($this->fixWith($with) as $name => $custom) {
            $this->relations[$name]->stitchIntoRecord(
                $atlas,
                $record,
                $custom
            );
        }
    }

    public function stitchIntoRecordSet(Atlas $atlas, RecordSet $recordSet, array $with)
    {
        foreach ($this->fixWith($with) as $name => $custom) {
            $this->relations[$name]->stitchIntoRecordSet(
                $atlas,
                $recordSet,
                $custom
            );
        }
        return $recordSet;
    }

    protected function fixWith($spec)
    {
        $with = [];
        foreach ($spec as $key => $val) {
            if (is_int($key)) {
                $with[$val] = null;
            } else {
                $with[$key] = $val;
            }
        }
        return $with;
    }
}