<?php
namespace Concrete\Package\DtMemberActivity\Controller\SinglePage\Dashboard\Users;

use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use Exception;
use User;
use Core;
use UserInfo;
use URL;
use stdClass;
use Localization;
use Concrete\Package\DtMemberActivity\Src\DtMemberLog;
use Concrete\Package\DtMemberActivity\Src\DtIgnoreList;

class Activity extends DashboardPageController
{
    public function view($uID = false)
    {
        $this->requireAsset('javascript', 'dt.tablesorter');
        $this->requireAsset('javascript', 'dt.tablesorter.widgets.alignchar');

        $this->requireAsset('css', 'dt.tablesorter');
        $this->requireAsset('css', 'dt.tablesorter.filter');


        if ($uID) {
            $user = User::getByUserID(Loader::helper('security')->sanitizeInt($uID));
            if($user instanceof User) {
                $ui = UserInfo::getByID(Loader::helper('security')->sanitizeInt($uID));
            }
        }


        if (is_object($ui)) {
            $this->set('pageTitle', t('View %s\'s activity', $user->getUserName()));
            $this->set('userActivities', DtMemberLog::getActivityByUser($uID));
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

    public function addIgnore()
    {
        $data = $this->post();
        $status = 'error';
        if($data['path'] && $data['match']) {
            if (!DtIgnoreList::isListed($data['path'])) {
                $ignore = new DtIgnoreList;
                $ignore->setPath($data['path']);
                if($data['match'] == 'exact') $ignore->setExact(true); else $ignore->setExact(false);
                $ignore->save();
                $status = $ignore->getID();
            } else {
                $status = 'already listed';
            }
        }
        echo json_encode(['status'=>$status]);
        die();
    }

    public function removeIgnore($path = NULL)
    {
        $data = $this->post();
        $ignore = DtIgnoreList::getByID($data['ID']);
        $ignore->remove();
        echo json_encode(['status'=>'ok']);
        die();
    }

    public function getIgnoreList($dump = NULL)
    {
        $allIgnores = DtIgnoreList::getAll();

        ?>
        <div class="ccm-ui">
            <table id="ignoreTable" class="table table-stripped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th><?= t('Match') ?></th>
                    <th><?= t('Path') ?></th>
                    <th></th>
                </tr>
                </thead>

                <tbody>
                <?php
                foreach ($allIgnores as $ignore) {
                    ?>
                    <tr>
                        <td><?= $ignore->getID() ?></td>
                        <td><?= $ignore->isExact() ? t('Exact') : t('Contains') ?></td>
                        <td><?= $ignore->getPath() ?></td>
                        <td style="width: 50px">
                            <a href="#" class="icon-link removeIgnore" data-ignoreid="<?= $ignore->getID() ?>"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>

            <div class="form-group has-feedback">
                <label class="control-label"><?= t('Add Path') ?></label>
                <input id="addPath" type="text" class="form-control">
            </div>
            <div class="form-group has-feedback">
                <label class="control-label"><?= t('Match Type') ?></label><br>
                <label class="radio-inline"><input type="radio" name="match" value="exact"><?= t('Exact') ?></label>
                <label class="radio-inline"><input type="radio" name="match" value="contains"><?= t('Contains') ?></label>
            </div>
            <button id="addIgnore" type="button" class="btn btn-primary"><?= t('Add') ?></button>

        </div>
        <script>
            $(document).on('click','.removeIgnore', function() {
                var $ignore = $(this);
                var ignoreLink = '<?= $this->action('removeIgnore') ?>';
                var data = {
                    'ID' : $ignore.attr('data-ignoreid')
                };
                $.post(ignoreLink, data, function (r) {
                    response = $.parseJSON(r);
                    console.log(response);
                    if(response['status'] == 'ok') {
                        $ignore.parent().parent().remove();
                    }
                });
            });

            $('#addIgnore').on('click', function() {
                var ignoreLink = '<?= $this->action('addIgnore') ?>';
                var data = {
                    'path' : $('#addPath').val(),
                    'match' : $('input[name=match]:checked').val()
                };
                $.post(ignoreLink, data, function (r) {
                    response = $.parseJSON(r);
                    console.log(response);
                    if(response['status'] !== 'error') {
                        $('#ignoreTable tbody').append('<tr><td>'+response["status"]+'</td><td>'+data["match"].charAt(0).toUpperCase()+data["match"].slice(1)+'</td><td>'+data["path"]+'</td><td style="width: 50px"><a href="#" class="icon-link removeIgnore" data-ignoreid="'+response["status"]+'"><i class="fa fa-trash-o"></i></a></td></tr>');
                        $('#addPath').val('');
                    }
                });
            });
        </script>
        <?php
        die();
    }

}
