<?php
namespace Concrete\Package\DtMemberActivity\Controller\SinglePage\Dashboard\Users;

use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Imagine\Image\Box;
use Loader;
use Exception;
use User;
use Core;
use UserInfo;
use URL;
use stdClass;
use Localization;
use Concrete\Package\DtMemberActivity\Src\DtMemberLog;

class Activity extends DashboardPageController
{
    public function view($uID = false)
    {
        $this->requireAsset('javascript', 'dt.tablesorter');
        $this->requireAsset('javascript', 'dt.tablesorter.widgets');
        $this->requireAsset('javascript', 'dt.tablesorter.widgets.alignchar');
        $this->requireAsset('javascript', 'dt.tablesorter.extras.pager');

        //$this->requireAsset('css', 'dt.tablesorter');

        if ($uID) {
            $user = User::getByUserID(Loader::helper('security')->sanitizeInt($uID));
            if($user instanceof User) {
                $ui = UserInfo::getByID(Loader::helper('security')->sanitizeInt($uID));
            }
        }


        if (is_object($ui)) {
            $this->set('pageTitle', t('View %s\'s activity', $user->getUserName()));

            $this->set('message', 'uID given');
            $this->set('userActivity',['activity'=>'yes']);

        } else {
            $userlist = new \Concrete\Core\User\UserList();
            $userlist->sortByDateAdded();
            $users = $userlist->getResults();
            $userslastactivity = [];
            foreach ($users as $user) {
                $userslastactivity[$user->getUserID()] = DtMemberLog::getLastActivityByUser($user->getUserID());
            }
            $this->set('users', $users);
            $this->set('userslastactivity', $userslastactivity);
        }
    }

}
