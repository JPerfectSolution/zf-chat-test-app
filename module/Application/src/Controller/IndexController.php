<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\Model\MessageTable;
use Application\Model\Message;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class IndexController extends AbstractActionController
{
    private $table;

    public function __construct(MessageTable $table) 
    {
        $this->table = $table;
    }

    public function indexAction()
    {
        return new ViewModel([
            'messages' => $this->table->fetchAll(),
        ]);
    }

    public function addAction()
    {
        $message = new Message();
        $message->text = $this->getRequest()->getPost('message', '');
        $this->table->saveMessage($message);
        
        $messages=[];
        $result = $this->table->fetchAll();
        
        for($cnt = $result->count(); $cnt > 0; $cnt-- ) {
            $row = $result->current();
            $messages[]= $row;
            $result->next();
        }

        $response = $this->getResponse();
        $response->getHeaders()->addHeaderLine( 'Content-Type', 'application/json' );
        $response->setContent(json_encode($messages));
        return $response;
    }
}
