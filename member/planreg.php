<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$newsprstr = '';
$ceknewmpid = getmpidflow($sesref['mpid']);
if ($ceknewmpid != $sesref['mpid']) {
    $sesnewref = getmbrinfo('', '', $ceknewmpid);
    $newsprstr = "<blockquote class='text-primary text-left'>You were referred by <strong>{$sesref['username']}</strong> who has a maximum number of referrals. The system has assigned <strong>{$sesnewref['username']}</strong> as your new sponsor.</blockquote>";
    $idref = $sesnewref['id'];
}

if ($bpprow['planstatus'] == 1 && $FORM['doid'] > 0) {
    regmbrplans($mbrstr, $sesref['mpid'], $bpprow['ppid']);
    redirpageto('index.php?hal=planpay');
    exit;
}
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-unlock-alt"></i> <?php echo myvalidate($LANG['m_planreg']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-12">
            <article class="article article-style-b">
                <div class="article-header">
                    <div class="article-image" data-background="<?php echo myvalidate($planlogo); ?>">
                    </div>
                    <div class="article-badge">
                        <span class="article-badge-item bg-danger">
                            <?php echo myvalidate($bpprow['currencysym'] . $bpprow['regfee'] . ' ' . $bpprow['currencycode']); ?>
                        </span>
                        <?php
                        if ($sesref['username'] && $mbrstr['username'] != $sesref['username']) {
                            ?>
                            <span class="article-badge-item bg-warning">
                                Referred by <?php echo ($mbrstr['mpid'] > 0) ? myvalidate($mbrstr['username']) : myvalidate($sesref['username']); ?>
                            </span>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="article-details">
                    <div class="article-title">
                        <h4><?php echo myvalidate($bpprow['ppname']); ?></h4>
                    </div>
                    <p><?php echo myvalidate($bpprow['planinfo']); ?></p>
                    <div class="article-cta">
                        <?php echo myvalidate($newsprstr); ?>

                        <?php
                        if ($mbrstr['idmbr'] == $mbrstr['id'] && $mbrstr['mpstatus'] > 0) {
                            ?>
                            <span class="badge badge-secondary">
                                REGISTERED
                            </span>
                            <?php
                            if ($mbrstr['mpstatus'] == 1) {
                                ?>
                                <span class="badge badge-success">
                                    ACTIVE <i class="fas fa-fw fa-check"></i>
                                </span>
                                <?php
                            } else {
                                ?>
                                <span class="badge badge-warning">
                                    INACTIVE <i class="fas fa-fw fa-exclamation"></i>
                                </span>
                                <?php
                            }
                            ?>
                            <?php
                        } elseif ($mbrstr['idmbr'] == $mbrstr['id'] && $mbrstr['mpstatus'] == 0) {
                            ?>
                            <a href="index.php?hal=planpay&doid=<?php echo myvalidate($bpprow['ppid']); ?>" class="btn btn-lg btn-danger">MAKE PAYMENT <i class="fas fa-fw fa-long-arrow-alt-right"></i></a>
                            <?php
                        } else {
                            if ($bpprow['planstatus'] == 1) {
                                $refbystr = ($sesref['username']) ? "Your referrer is <strong>{$sesref['username']}</strong><br />" : '';
                                $refbystr .= ($sesnewref['username']) ? "Your assigned sponsor is <strong>{$sesnewref['username']}</strong><br />" : '';
                                ?>
                                <a href="javascript:;" data-href="index.php?hal=planreg&doid=<?php echo myvalidate($bpprow['ppid']); ?>" class="btn btn-lg btn-primary bootboxconfirm" data-poptitle="<?php echo myvalidate($bpprow['ppname']); ?> - <?php echo myvalidate($bpprow['currencysym'] . $bpprow['regfee'] . ' ' . $bpprow['currencycode']); ?>" data-popmsg="<?php echo myvalidate($refbystr); ?><p>Are you sure want to register to this membership?</p>">REGISTER <i class="fas fa-fw fa-long-arrow-alt-right"></i></a>
                                <?php
                            } else {
                                ?>
                                <span class="badge badge-danger"><i class="fa fa-fw fa-times"></i> REGISTRATION DISABLE</span>
                                <?php
                            }
                        }
                        ?>

                    </div>
                </div>
            </article>

        </div>
    </div>
</div>
