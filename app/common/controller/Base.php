<?php
namespace app\common\controller;
class Base extends Common
{


	public function __construct()
    {

	

        hook('webSite');
        parent::__construct();
		
		

        $this->member_service = model('member/Member','service');

        $this->member=$this->member_service->inits();

        $this->assign('member',$this->member);



	}

    public function _empty(){


        error_status(404);

    }

}
