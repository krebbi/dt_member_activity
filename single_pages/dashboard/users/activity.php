<?php
    $tp = Loader::helper('concrete/user');
    $dh = Core::make('helper/date');
    if ($tp->canAccessUserSearchInterface()) {
        if(empty($userActivity)) { ?>
        <div class="ccm-dashboard-content-full" data-search="users">
            <style type="text/css">
                .tablesorter-header:focus {
                    outline:none !important;
                }
            </style>
            <table border="0" cellspacing="0" cellpadding="0" class="ccm-search-results-table dt-tablesorter tablesorter">
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
                        <td><span class="hidden"><?= $user->getAttribute('dt_last_login') ? (new DateTime($user->getAttribute('dt_last_login')))->getTimestamp() : "0" ?></span><?= $dh->formatDateTime($user->getAttribute('dt_last_login'), true, true) ?></td>
                        <td><span class="hidden"><?= $user->getAttribute('dt_last_activity') ? (new DateTime($user->getAttribute('dt_last_activity')))->getTimestamp() : "0" ?></span><?= $dh->formatDateTime($user->getAttribute('dt_last_activity'), true, true) ?>
                        <br><?= $userslastactivity[$user->getUserID()] ? $userslastactivity[$user->getUserID()]->getTypeName() : "" ?>
                        <br><?= $userslastactivity[$user->getUserID()] ? $userslastactivity[$user->getUserID()]->getTypePath() : "" ?>
                        </td>
                        <td><?= $user->getLastIPAddress() ?></td>
                    </tr>

            <?php }
            ?>
                </tbody>
            </table>
            <ul class="uk-pagination ts_pager">
                <li data-uk-tooltip title="Select Page">
                    <select class="ts_gotoPage ts_selectize"></select>
                </li>
                <li class="first"><a href="javascript:void(0)"><i class="uk-icon-angle-double-left"></i></a></li>
                <li class="prev"><a href="javascript:void(0)"><i class="uk-icon-angle-left"></i></a></li>
                <li><span class="pagedisplay"></span></li>
                <li class="next"><a href="javascript:void(0)"><i class="uk-icon-angle-right"></i></a></li>
                <li class="last"><a href="javascript:void(0)"><i class="uk-icon-angle-double-right"></i></a></li>
                <li data-uk-tooltip title="Page Size">
                    <select class="pagesize ts_selectize">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="all">all</option>
                    </select>
                </li>
            </ul>
        </div>
            <script>
                $(window).on('load', function() {

                    var pagerOptions = {
                        container: $(".ts_pager"),
                        output: '{startRow} - {endRow} / {filteredRows} ({totalRows})',
                        fixedHeight: true,
                        removeRows: false,
                        cssGoto: '.ts_gotoPage'
                    };

                    // Initialize tablesorter
                    var ts_users = $("table.dt-tablesorter")
                        .tablesorter({
                            theme: 'altair',
                            widthFixed: true,
                            widgets: ['zebra', 'filter']
                        })
                        // initialize the pager plugin
                        .tablesorterPager(pagerOptions)
                        .on('pagerComplete', function(e, filter){
                            // update selectize value
                            if(typeof selectizeObj !== 'undefined' && selectizeObj.data('selectize')) {
                                selectizePage = selectizeObj[0].selectize;
                                selectizePage.setValue($('select.ts_gotoPage option:selected').index() + 1, false);
                            }
                        });

                    // replace 'goto Page' select
                    function createPageSelectize() {
                        selectizeObj = $('select.ts_gotoPage')
                            .val($("select.ts_gotoPage option:selected").val())
                            .after('<div class="selectize_fix"></div>')
                            .selectize({
                                hideSelected: true,
                                onDropdownOpen: function($dropdown) {
                                    $dropdown
                                        .hide()
                                        .velocity('slideDown', {
                                            duration: 280,
                                            easing: easing_swiftOut
                                        })
                                },
                                onDropdownClose: function($dropdown) {
                                    $dropdown
                                        .show()
                                        .velocity('slideUp', {
                                            duration: 280,
                                            easing: easing_swiftOut
                                        });
                                    // hide tooltip
                                    $('.uk-tooltip').hide();
                                }
                            });
                    }
                    createPageSelectize();

                    // replace 'pagesize' select
                    $('.pagesize.ts_selectize')
                        .after('<div class="selectize_fix"></div>')
                        .selectize({
                            hideSelected: true,
                            onDropdownOpen: function($dropdown) {
                                $dropdown
                                    .hide()
                                    .velocity('slideDown', {
                                        duration: 280,
                                        easing: easing_swiftOut
                                    })
                            },
                            onDropdownClose: function($dropdown) {
                                $dropdown
                                    .show()
                                    .velocity('slideUp', {
                                        duration: 280,
                                        easing: easing_swiftOut
                                    });

                                // hide tooltip
                                $('.uk-tooltip').hide();
                                if(typeof selectizeObj !== 'undefined' && selectizeObj.data('selectize')) {
                                    selectizePage = selectizeObj[0].selectize;
                                    selectizePage.destroy();
                                    $('.ts_gotoPage').next('.selectize_fix').remove();
                                    setTimeout(function() {
                                        createPageSelectize()
                                    })
                                }

                            }
                        });
                });
            </script>
    <?php
        }
        else {

            // Detailed User Activity
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
