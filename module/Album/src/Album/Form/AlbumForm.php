<?php
namespace Album\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;


class AlbumForm extends Form
{

    /**
     * AlbumForm constructor.
     * @param null $name
     */
        public function __construct($name = null)
        {
            // we want to ignore the name passed
            parent::__construct('album');

            $this->setAttribute('method', 'post');
            $this->setAttribute('enctype', 'multipart/form-data');
            $this->setInputFilter(new AlbumFormFilter());

            $this->add(array(
                'name' => 'id',
                'attributes' => array(
                    'type'  => 'hidden',
                ),
            ));

            $this->add(array(
                'name' => 'picture',
                'attributes' => array(
                    'type'  => 'file',
                ),
                'options' => array(
                    'label' => 'Picture',
                ),
            ));

            $this->add(array(
                'name' => 'artist',
                'attributes' => array(
                    'type'  => 'text',
                ),
                'options' => array(
                    'label' => 'Artist',
                ),
            ));

            $this->add(array(
                'name' => 'title',
                'attributes' => array(
                    'type'  => 'text',
                ),
                'options' => array(
                    'label' => 'Title',
                    'min' => 3,
                    'max' => 100,
                ),
            ));

            $this->add(array(
                'name' => 'submit',
                'attributes' => array(
                    'type'  => 'submit',
                    'value' => 'Go',
                    'id' => 'submitbutton',
                ),
            ));

        }
}