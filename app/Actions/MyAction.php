<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class MyAction extends AbstractAction
{
    public function getTitle()
    {
        return 'Enquery Details';
    }

    public function getIcon()
    {
        return 'voyager-eye';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-warning pull-right',
        ];
    }

    public function getDefaultRoute()
    {
        return route('enquery.detail', ['enquiry_id' =>  $this->data->id]);
    }

    public function shouldActionDisplayOnDataType()
    {
        return $this->dataType->slug == 'enqueries';
    }
}