<?php 

namespace Concrete\Package\DtMemberActivity;

use Package;
use SinglePage;

class Controller extends Package
{
    protected $pkgHandle = 'dt_member_activity';
    protected $appVersionRequired = '5.7.0.4';
    protected $pkgVersion = '0.9.0';

    public function getPackageName()
    {
        return t('Track Member Activity');
    }

    public function getPackageDescription()
    {
        return t('All Member Activities will be tracked.<br>created by <a href="https://www.datatainment.de" target="_blank">datatainment</a>');
    }

    public function install()
    {
        $pkg = parent::install();
        $page = SinglePage::add('/dashboard/users/activity', $pkg);
        $page->updateCollectionName(t('User Activity'));
        return $pkg;
    }
}
