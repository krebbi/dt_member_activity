<?php
    $tp = Loader::helper('concrete/user');
    $dh = Core::make('helper/date');
    if ($tp->canAccessUserSearchInterface()) {
        if(empty($userActivity)) { ?>
        <div class="ccm-dashboard-content-full" data-search="users">
            <table border="0" cellspacing="0" cellpadding="0" class="ccm-search-results-table">
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
                ?>
                    <tr>
                        <td><a data-user-name="<?= $user->getUserName() ?>" data-user-email="<?= $user->getUserEmail()?>" data-user-id="<?= $user->getUserID()?>" href="<?= URL::to('/dashboard/users/activity',$user->getUserID()) ?>"><?= $user->getUserName()?></a></td>
                        <td><a href="mailto:<?= $user->getUserEmail() ?>"><?= $user->getUserEmail() ?></a></td>
                        <td><?= $user->getNumLogins() ?></td>
                        <td><?= $dh->formatDateTime($user->getAttribute('dt_last_login'), true, true) ?></td>
                        <td><?= $dh->formatDateTime($user->getAttribute('dt_last_activity'), true, true) ?>
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
    <?php
        }
        else {
            ?>

            <div class="ccm-dashboard-header-buttons">
                <a href="<?php echo URL::to('/dashboard/users/activity') ?>"
                   class="btn btn-primary"><?php echo t("Back") ?></a>
            </div>

            <?php
        }

    } else { ?>
        <p><?php echo t('You do not have access to user search. This setting may be changed in the access section of the dashboard settings page.') ?></p>
    <?php } ?>
