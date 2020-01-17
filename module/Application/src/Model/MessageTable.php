<?php

namespace Application\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\Sql\Select;

class MessageTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $select = new Select($this->tableGateway->getTable());
        $select->order('created DESC');
        return $this->tableGateway->selectWith($select);
    }

    public function getMessage($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (!$row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    public function saveMessage(Message $message)
    {
        $data = [
            'created' => date('Y-m-d H:i:s'),
            'text' => $message->text,
        ];

        $id = (int) $message->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            
            $select = new Select($this->tableGateway->getTable());
            $select->order('created ASC');
            $result = $this->tableGateway->selectWith($select);
            
            for($cnt = $result->count(); $cnt > 10; $cnt-- ) {
                $row = $result->current();
                $this->deleteMessage($row->id);
                $result->next();
            }
            return;
        }

        try {
            $this->getMessage($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update message with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteMessage($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}
