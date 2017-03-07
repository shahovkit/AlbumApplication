<?php

namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\Controller\Plugin\FlashMessenger;
use Zend\View\Model\ViewModel;
use Album\Form\AlbumForm;
use Album\Entity\Album;

class AlbumController extends AbstractActionController
{
    public function indexAction()
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $albums = $objectManager
            ->getRepository('\Album\Entity\Album')->findAll();

        $view = new ViewModel(array(
            'albums' => $albums,
        ));

        return $view;

    }


    public function addAction()
    {
        $form = new AlbumForm(); //Create new form
        $form->get('submit')->setValue('Add');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost()); //get form data
            if ($form->isValid()) {
                $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                $album = new Album(); //create object entity

                //Getting format file after dot
                $filename = $_FILES['picture']['name'];
                $postfix = explode('.', $filename);
                $postfix = $postfix[1];

                $album->exchangeArray($form->getData());//add album in object from form

                if($filename) {
                    $dir_upload = tempnam($_SERVER['DOCUMENT_ROOT'] . '/img/uploads/', 'up_');//Generate random name file
                    unlink($dir_upload);//delete temp file
                    $dir_upload = $dir_upload . '.' . $postfix; //Concatenate dir file with format


                    move_uploaded_file($_FILES['picture']['tmp_name'], $dir_upload); //Upload image

                    $dir_upload = str_replace($_SERVER['DOCUMENT_ROOT'], '', $dir_upload);//delete server dir from file name
                    $album->setPicture($dir_upload);//set file dir
                }

                $objectManager->persist($album); //save object DB
                $objectManager->flush(); //Clear

                $message = 'Album succesfully added!';
                $this->flashMessenger()->addSuccessMessage($message);

            }

            return $this->redirect()->toRoute('album');
        } else {
            $message = 'Error while saving album!';
            $this->flashMessenger()->addErrorMessage($message);


        }

        return array('form' => $form);
    }


    public function editAction()
    {
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

        $id = (int) $this->params()->fromRoute('id', 0);//get album id from route

        if (0 === $id) { //validate id
            return $this->redirect()->toRoute('album', ['action' => 'add']);
        }

        try {
            $album = $objectManager->find('Album\Entity\Album', $id); //get album with this id
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('album', ['action' => 'index']);
        }

        $form = new AlbumForm();//create new form
        $form->bind($album);//fill form from db
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();

        $picture = $album->getPicture();

        $view = new ViewModel([
            'id' => $id,
            'form' => $form,
            'picture'=> $picture,
        ]);


        if ($request->isPost()) {

            $form->setData($request->getPost());

            //update data changed form fields
            $view = new ViewModel([
                'id' => $id,
                'form' => $form,
                'picture'=> $picture,
            ]);


            if ($form->isValid()) {

                $filename = $_FILES['picture']['name'];
                $postfix = explode('.', $filename);
                $postfix = $postfix[1];

                if($filename) {
                    $file_dir = $_SERVER['DOCUMENT_ROOT'] . $album->getPicture();
                    if($file_dir)
                    {
                        unlink( $file_dir); //delete file
                    }

                    $dir_upload = tempnam($_SERVER['DOCUMENT_ROOT'] . '/img/uploads/', 'up_');//Generate random name file
                    unlink($dir_upload);//delete temp file
                    $dir_upload = $dir_upload . '.' . $postfix; //Concatenate dir file with format


                    move_uploaded_file($_FILES['picture']['tmp_name'], $dir_upload); //Upload image

                    $dir_upload = str_replace($_SERVER['DOCUMENT_ROOT'], '', $dir_upload);//delete server dir from file name
                    $album->setPicture($dir_upload);//set file dir
                    //update preview edit
                    // $view = new ViewModel([
                    //     'id' => $id,
                    //     'form' => $form,
                    //     'picture'=> $dir_upload,
                    // ]);

                    $message = 'Album succesfully changed!';
                    $this->flashMessenger()->addSuccessMessage($message);
                }


                $album->exchangeArray($form->getData());//edit object
                $objectManager->persist($album);//save edit object to db
                $objectManager->flush();//clear

                return $this->redirect()->toRoute('album', ['action' => 'index']);
            }
        }
        return $view;

    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            $this->flashMessenger()->addErrorMessage('Album id doesn\'t set');
            return $this->redirect()->toRoute('album');
        }
        $objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id'); //get id from request POST
                try {
                    $album = $objectManager->find('Album\Entity\Album', $id);//get album
                    $file_dir = $_SERVER['DOCUMENT_ROOT'] . $album->getPicture();//create path to image
                    if($file_dir)
                    {
                        unlink( $file_dir); //delete file
                    }

                    $objectManager->remove($album); //remove from db
                    $objectManager->flush(); //clear

                }
                catch (\Exception $ex) {
                    $this->flashMessenger()->addErrorMessage('Error while deleting data');
                    return $this->redirect()->toRoute('album', array(
                        'action' => 'index'
                    ));
                }
                $this->flashMessenger()->addSuccessMessage('Album "'. $album->getTitle() . '" by artist "' .  $album->getArtist() . '" was succesfully deleted');
            }
            return $this->redirect()->toRoute('album');
        }
        return array(
            'album' => $objectManager->find('Album\Entity\Album', $id),
        );
    }
}