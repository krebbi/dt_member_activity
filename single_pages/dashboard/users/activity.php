<?php defined('C5_EXECUTE') or die("Access Denied.");

    $tp = Loader::helper('concrete/user');
    $dh = Core::make('helper/date');
    if ($tp->canAccessUserSearchInterface()) {
        if($users) { ?>
        <div class="ccm-dashboard-content-full">
            <div class="table-responsive">
                <table border="0" cellspacing="0" cellpadding="0" class="ccm-search-results-table tablesorter dt-tablesorter">
                    <thead>
                    <tr>
                        <th class="false"><a href="#"><?= t('Username') ?></a></th>
                        <th class="false"><a href="#"><?= t('Email') ?></a></th>
                        <th class="false"><a href="#"><?= t('# Logins') ?></a></th>
                        <th class="false"><a href="#"><?= t('Last Login') ?></a></th>
                        <th class="false"><a href="#"><?= t('Last Activity') ?></a></th>
                        <th class="false"><a href="#"><?= t('Last IP') ?></a></th>
                    </tr>
                </thead>
                <tbody>
            <?php
            foreach ($users as $user) {
                // 5.7 vs. 8.0 "IT WILL BE COMPATIBLE" BULLSHIT CHECK
                $lastLoginObj = $user->getAttribute('dt_last_login');
                if(!is_object($lastLoginObj) && $user->getAttribute('dt_last_login')) {
                    $lastLoginObj = new DateTime($user->getAttribute('dt_last_login'));
                }
                $lastActivityObj = $user->getAttribute('dt_last_activity');
                if(!is_object($lastActivityObj) && $user->getAttribute('dt_last_activity')) {
                    $lastActivityObj = new DateTime($user->getAttribute('dt_last_activity'));
                }

                ?>
                <tr>
                    <td><a data-user-name="<?= $user->getUserName() ?>" data-user-email="<?= $user->getUserEmail()?>" data-user-id="<?= $user->getUserID()?>" href="<?= URL::to('/dashboard/users/activity',$user->getUserID()) ?>"><?= $user->getUserName()?></a></td>
                    <td><a href="mailto:<?= $user->getUserEmail() ?>"><?= $user->getUserEmail() ?></a></td>
                    <td><?= $user->getNumLogins() ?></td>
                    <td><span class="hidden"><?= $lastLoginObj ? $lastLoginObj->getTimestamp() : "0" ?></span><?= $dh->formatDateTime($lastLoginObj, true, true) ?></td>
                    <td><span class="hidden"><?= $lastActivityObj ? $lastActivityObj->getTimestamp() : "0" ?></span><?= $dh->formatDateTime($lastActivityObj, true, true) ?>
                        <br><?= $userslastactivity[$user->getUserID()] ? $userslastactivity[$user->getUserID()]->getTypeName() : "" ?>
                        <br><?= $userslastactivity[$user->getUserID()] ? $userslastactivity[$user->getUserID()]->getTypePath() : "" ?>
                    </td>
                    <td><?= $user->getLastIPAddress() ?></td>
                </tr>

            <?php }
            ?>
                </tbody>
                </table>
            </div>
        </div>
            <div class="ccm-dashboard-header-buttons">
                <a id="ignoreList" href="#" class="btn btn-primary"><?php echo t("Ignore List") ?></a>
            </div>
            <script>
                $(window).on('load', function() {
                    // Initialize tablesorter
                    var $ts_users = $("table.dt-tablesorter")
                        .tablesorter({
                            widgets: ['zebra', 'filter']
                        })
                });

                $('#ignoreList').on('click', function() {
                    $.fn.dialog.open({
                        href: '<?= $view->action('getIgnoreList') ?>/'+ new Date().getTime(),
                        title: 'Ignore List',
                        width: '680',
                        height: '420',
                        modal: true,
                        buttons: [
                            {
                                text: '<?= t('Close') ?>',
                                click: function () {
                                    $(this).dialog('close');
                                }
                            }
                        ]
                    });
                    return false;
                });

            </script>
    <?php
        }
        else {
            // Detailed User Activity
            ?>

            <div class="ccm-dashboard-content-full" data-search="users">
                <div class="table-responsive">
                    <table border="0" cellspacing="0" cellpadding="0" class="ccm-search-results-table tablesorter dt-tablesorter">
                        <thead>
                        <tr>
                            <th class="false"><a href="#"><?= t('Date') ?></a></th>
                            <th class="false"><a href="#"><?= t('IP') ?></a></th>
                            <th class="false"><a href="#"><?= t('Type') ?></a></th>
                            <th class="false"><a href="#"><?= t('Name') ?></a></th>
                            <th class="false"><a href="#"><?= t('Path') ?></a></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($userActivities as $activity) {
                            ?>
                            <tr>
                                <td><span class="hidden"><?= $activity->getDate()->getTimestamp() ?></span><?= $dh->formatDateTime($activity->getDate(), true, true) ?></td>
                                <td><?= $activity->getIP() ?></td>
                                <td><?= t($activity->getType()) ?></td>
                                <td><?= t($activity->getTypeName()) ?></td>
                                <td><?= $activity->getTypePath() ?></td>
                            </tr>

                        <?php }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <script>
                $(window).on('load', function() {
                    // Initialize tablesorter
                    var $ts_users = $("table.dt-tablesorter")
                        .tablesorter({
                            widgets: ['zebra', 'filter']
                        })
                });
            </script>

            <div class="ccm-dashboard-header-buttons">
                <a href="<?php echo URL::to('/dashboard/users/activity') ?>"
                   class="btn btn-primary"><?php echo t("Back") ?></a>
            </div>

            <?php
        }

    } else { ?>
        <p><?php echo t('You do not have access to user search. This setting may be changed in the access section of the dashboard settings page.') ?></p>
    <?php } ?>
