<?php

namespace Admin\Models;

use Phalcon\Di\Injectable;
use MongoDB\BSON\ObjectId;

class User extends Injectable
{
    public function __construct()
    {
        $this->collection = $this->di->get('mongo')->users;
        $this->vals = [];
    }
    public function set($k, $v)
    {
        $this->vals[$k] = $v;
    }
    public function save()
    {
        $resp = [];
        $data = $this->vals;
        if (isset($data['_id'])) {
            $resp = $this->collection->updateOne(['_id'=>$data['_id']], ['$set' => $data], ['$upsert' => true]);
        } else {
            $resp = $this->collection->insertOne($data);
            $this->vals["_id"] = $resp->getInsertedId();
        }
        return $resp;
    }
    public function assign($arr, $keys = [])
    {
        if (count($keys) > 0) {
            foreach ($arr as $k => $v) {
                if (in_array($k, $keys)) {
                    $this->vals[$k] = $v;
                }
            }
        } else {
            foreach ($arr as $k => $v) {
                $this->vals[$k] = $v;
            }
        }
    }
    public function find($arr = [])
    {
        $ret = [];
        $data = $this->collection->find($arr);
        foreach ($data as $k => $v) {
            $ret[$k] = $v;
        }
        $retArr = [];
        foreach ($ret as $k => $v) {
            $retArr[$k] = json_decode(json_encode($ret[$k]), 1);
            $retArr[$k]["_id"] = $ret[$k]->_id;
        }
        return $retArr;
    }
    public function findFirst($arr = [])
    {
        $this->vals = $this->find($arr)[0];
    }
    public function findByID($id)
    {
        $this->findFirst(["_id" => new ObjectId($id)]);
    }
    public function delete()
    {
        $this->collection->deleteOne(["_id" => $this->vals["_id"]]);
    }
}
