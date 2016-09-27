<?php 

namespace Concrete\Package\DtMemberActivity;

use \Core;
use Concrete\Core\User\User;
use Concrete\Core\Package\Package;
use Concrete\Core\SinglePage;
use Concrete\Core\Asset\Asset;
use Concrete\Core\Asset\AssetList;
use Concrete\Package\DtMemberActivity\Src\DtMemberLog;

class Controller extends Package
{
    protected $pkgHandle = 'dt_member_activity';
    protected $appVersionRequired = '5.7.5.0';
    protected $pkgVersion = '0.9.4.0';

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
        self::installSinglePages($pkg);
        self::installUserAttributes($pkg);
        return $pkg;
    }

    public function upgrade()
    {
        $pkg = parent::upgrade();
        self::installSinglePages($pkg);
        self::installUserAttributes($pkg);
    }

    public function uninstall()
    {
        parent::uninstall();
        $db = \Database::connection();
        $db->query('drop table DtMemberActivityLog');
        self::uninstallUserAttributes();
    }

    public function on_start()
    {
        $al = AssetList::getInstance();

        $ph = Array('position' => Asset::ASSET_POSITION_HEADER, 'minify' => false, 'combine' => false);
        $pf = Array('position' => Asset::ASSET_POSITION_FOOTER, 'minify' => false, 'combine' => false);

        $al->register( 'javascript', 'dt.tablesorter', 'src/tablesorter/js/jquery.tablesorter.combined.min.js', $pf, $this );
        $al->register( 'javascript', 'dt.tablesorter.widgets', 'src/tablesorter/js/jquery.tablesorter.widgets.js', $pf, $this );
        $al->register( 'javascript', 'dt.tablesorter.widgets.alignchar', 'src/tablesorter/js/widgets/widget-alignChar.min.js', $pf, $this );
        $al->register( 'javascript', 'dt.tablesorter.extras.pager', 'src/tablesorter/js/extras/jquery.tablesorter.pager.min.js', $pf, $this );

        $al->register( 'css', 'dt.tablesorter', 'src/tablesorter/css/theme.c5.css', $ph, $this );
        $al->register( 'css', 'dt.tablesorter.filter', 'src/tablesorter/css/filter.formatter.min.css', $ph, $this );


        \Events::addListener(
            'on_page_view',
            function ($e)
            {
                $u = new User();
                if($u->getUserID() !== NULL) {
                    $page = $e->getPageObject();
                    $path = $_SERVER['REQUEST_URI'];

                    $log = new DtMemberLog();
                    $log->setType('Collection');
                    $log->setTypeID($page->getCollectionID());
                    $log->setTypeName($page->getCollectionName());
                    $log->setTypePath($path);
                    $log->setUserID($u->getUserID());
                    $log->setDate((new \DateTime("now", new \DateTimeZone(\Concrete\Core\Localization\Service\Date::getTimezoneID('app')) ))->setTimeZone(new \DateTimeZone('UTC')));
                    $log->setUserName($u->getUserName());
                    $log->setUserEmail($u->getUserInfoObject()->getUserEmail());
                    $log->setIP($u->getUserInfoObject()->getLastIPAddress());
                    $log->save();
                    $u->getUserInfoObject()->setAttribute('dt_last_activity',$log->getDate()->format('Y-m-d H:i:s'));
                }
            }
        );

        \Events::addListener(
            'on_file_download',
            function ($e)
            {
                $u = new User();
                if($u->getUserID() !== NULL) {
                    $file = $e->getFileVersionObject();
                    $log = new DtMemberLog();

                    $log->setType('File');
                    $log->setTypeID($file->getFileID());
                    $log->setTypeName($file->getFilename());
                    $log->setTypePath($file->getRelativePath());
                    $log->setUserID($u->getUserID());
                    $log->setDate((new \DateTime("now", new \DateTimeZone(\Concrete\Core\Localization\Service\Date::getTimezoneID('app')) ))->setTimeZone(new \DateTimeZone('UTC')));
                    $log->setUserName($u->getUserName());
                    $log->setUserEmail($u->getUserInfoObject()->getUserEmail());
                    $log->setIP($u->getUserInfoObject()->getLastIPAddress());
                    $log->save();
                    $u->getUserInfoObject()->setAttribute('dt_last_activity',$log->getDate()->format('Y-m-d H:i:s'));
                }
            }
        );

        \Events::addListener(
            'on_user_login',
            function ($e)
            {
                $u = $e->getUserObject();

                if($u->getUserID() !== NULL) {
                    $log = new DtMemberLog();

                    $log->setType('Login');
                    $log->setTypeID('0');
                    $log->setTypeName('Login');
                    $log->setTypePath('/login');
                    $log->setUserID($u->getUserID());
                    $log->setDate((new \DateTime("now", new \DateTimeZone(\Concrete\Core\Localization\Service\Date::getTimezoneID('app')) ))->setTimeZone(new \DateTimeZone('UTC')));
                    $log->setUserName($u->getUserName());
                    $log->setUserEmail($u->getUserInfoObject()->getUserEmail());
                    $log->setIP($u->getUserInfoObject()->getLastIPAddress());
                    $log->save();
                    $u->getUserInfoObject()->setAttribute('dt_last_login',$log->getDate()->format('Y-m-d H:i:s'));
                }
            }
        );
    }

    public static function installSinglePages($pkg)
    {
        $page = \SinglePage::add('/dashboard/users/activity', $pkg);
        $page->updateCollectionName(t('Member Activity'));
    }

    public static function installUserAttributes($pkg)
    {
        //user attributes
        $uakc = AttributeKeyCategory::getByHandle('user');
        $uakc->setAllowAttributeSets(AttributeKeyCategory::ASET_ALLOW_MULTIPLE);

        //define attr group, and the different attribute types we'll use
        $attrSet = AttributeSet::getByHandle('dt_member_activity');
        if (!is_object($attrSet)) {
            $attrSet = $uakc->addSet('dt_member_activity', t('datatainment Member Activity'), $pkg);
        }
        $text = AttributeType::getByHandle('text');
        $address = AttributeType::getByHandle('address');
        $checkbox = AttributeType::getByHandle('boolean');
        $date = AttributeType::getByHandle('date');


        self::installUserAttribute('dt_last_activity', t('Last Activity'), $date, $pkg, $attrSet,[
            'uakProfileEdit' => false,
            'akIsSearchable' => true
        ]);
        self::installUserAttribute('dt_last_login', t('Last Login'), $date, $pkg, $attrSet,[
            'uakProfileEdit' => false,
            'akIsSearchable' => true
        ]);
    }

    public static function uninstallUserAttributes()
    {
        AttributeSet::getByHandle('dt_member_activity')->delete();
        UserAttributeKey::getByHandle('dt_last_activity')->delete();
        UserAttributeKey::getByHandle('dt_last_login')->delete();
    }

    public static function installUserAttribute($handle, $name, $type, $pkg, $set, $data = null)
    {
        $attr = UserAttributeKey::getByHandle($handle);
        if (!is_object($attr)) {

            $newdata = [
                'akHandle' => $handle,
                'akName' => t($name),
                'akIsSearchable' => false,
                'uakProfileEdit' => true,
                'uakProfileEditRequired' => false,
                'uakRegisterEdit' => false,
                'uakProfileEditRequired' => false,
                'akCheckedByDefault' => true,
            ];

            if(data) {
                foreach($data as $key => $value) {
                    $newdata[$key] = $value;
                }
            }

            UserAttributeKey::add($type, $newdata, $pkg)->setAttributeSet($set);
        }
    }
}
