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
use Permissions;
use PermissionKey;
use UserAttributeKey;
use Localization;
use \Concrete\Controller\Search\Users as SearchUsersController;
use \Concrete\Core\User\EditResponse as UserEditResponse;

class Activity extends DashboardPageController
{
    public function view($uID = false)
    {
        if ($uID) {
            $user = User::getByUserID($uID);
            $this->set('pageTitle', t('View %s', $user->getUserName()));

            $this->set('message', 'uID given');
            $this->set('userActivity',['activity'=>'yes']);
        }

        $this->requireAsset('select2');

        $ui = $this->user;
        if (is_object($ui)) {

            // or whatever... just display the fucking user stats!

            $dh = Core::make('helper/date');
            /* @var $dh \Concrete\Core\Localization\Service\Date */
            $this->requireAsset('core/app/editable-fields');
            $uo = $this->user->getUserObject();
            $groups = array();
            foreach ($uo->getUserGroupObjects() as $g) {
                $obj = new stdClass();
                $obj->gDisplayName = $g->getGroupDisplayName();
                $obj->gID = $g->getGroupID();
                $obj->gDateTimeEntered = $dh->formatDateTime($g->getGroupDateTimeEntered($this->user));
                $groups[] = $obj;
            }
            $this->set('groupsJSON', Loader::helper('json')->encode($groups));
            $attributes = UserAttributeKey::getList(true);
            $this->set('attributes', $attributes);
            $this->set('pageTitle', t('View/Edit %s', $this->user->getUserDisplayName()));

        } else {
            $cnt = new SearchUsersController();
            $cnt->search();
            $this->set('searchController', $cnt);
            $result = $cnt->getSearchResultObject();
            if (is_object($result)) {
                $object = $result->getJSONObject();
                $result = Loader::helper('json')->encode($object);
                $this->addFooterItem(
                    "<script type=\"text/javascript\">
                        $(function () {
                            $('div[data-search=users]').concreteAjaxSearch({
                                result: " . $result . ",
                                onLoad: function (concreteSearch) {
                                    concreteSearch.\$element.on('click', 'a[data-user-id]', function () {
                                        window.location.href='"
                                            . rtrim(URL::to('/dashboard/users/activity'), '/')
                                            . "/' + $(this).attr('data-user-id');
                                        return false;
                                    });
                                }
                            });
                        });
                    </script>"
                );
            }
        }
    }

}
