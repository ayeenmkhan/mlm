<?php
if (!defined('OK_LOADME')) {
    die('o o p s !');
}

$langdir = INSTALL_PATH . "/common/lang";
$langid = $templangid = mystriptag($FORM['langid']);

if (isset($FORM['dosubmit']) and $FORM['dosubmit'] == '1') {

    extract($FORM);
    $in_date = date('Y-m-d H:i', time() + (3600 * $cfgrow['time_offset']));

    $translation_str = mystriptag($translation_str);
    $translation_author = mystriptag($translation_author);
    $lang_iso = mystriptag($lang_iso);
    $lang_iso = (strlen($lang_iso) != 2) ? 'en' : strtolower($lang_iso);

    $langfcnt = <<<INI_HTML
<?php

if (!defined('OK_LOADME')) {
    die('o o p s !');
}

\$LANG = array();

// Translation Details
\$translation_str = '{$translation_str}';
\$translation_author = '{$translation_author}';
\$translation_version = '{$translation_version}';
\$translation_update = '{$translation_update}';
\$translation_stamp = '{$in_date}';

// Character encoding, example: utf-8, iso-8859-1
\$LANG['lang_iso'] = "{$lang_iso}";
\$LANG['lang_charset'] = "{$lang_charset}";

// ----------------
// Array of Language
// ----------------

INI_HTML;

    $TEMPLANG = $LANG;
    $langloadf = INSTALL_PATH . '/common/en.lang.php';
    if (file_exists($langloadf)) {
        include_once($langloadf);
    }
    $langval = array_filter($langval);
    $langval = array_merge($LANG, $langval);
    $LANG = $TEMPLANG;

    foreach ($langval as $key => $value) {
        if ($key == 'lang_iso' || $key == 'lang_charset') {
            continue;
        }
        $langfcnt .= "\$LANG['{$key}'] = \"" . mystriptag($value) . "\";" . chr(13);
    }

    $nlangfile = $langdir . "/{$lang_iso}.lang.php";
    file_put_contents($nlangfile, $langfcnt);

    $data = $langarr = array();

    if ($isavallang == 1) {
        $langarr[$lang_iso] = $translation_str;
        $newlangarr = array_merge((array) $langlistarr, $langarr);
        $newlangarr = array_unique($newlangarr);
    } else {
        unset($langlistarr[$lang_iso]);
        $newlangarr = array_unique($langlistarr);
    }

    $newlangarr = json_encode($newlangarr);
    //echo '<em>';
    //print_r($newlangarr);
    //echo '</em>';
    //die($cfgrow['cfgtoken']);

    $langlist = base64_encode($newlangarr);
    $data['cfgtoken'] = put_optionvals($cfgrow['cfgtoken'], 'langlist', $langlist);

    if ($isdeflang != '') {
        $data['langiso'] = ($isdeflang == '-') ? $lang_iso : strtolower(mystriptag($isdeflang));
    }
    $update = $db->update(DB_TBLPREFIX . '_configs', $data, array('cfgid' => $didId));

    redirpageto("index.php?hal={$hal}&langid={$langid}");
    exit;
}
?>

<div class="section-header">
    <h1><i class="fa fa-fw fa-flag"></i> <?php echo myvalidate($LANG['a_languagelist']); ?></h1>
</div>

<div class="section-body">
    <div class="row">
        <div class="col-md-4">	
            <div class="card">
                <div class="card-header">
                    <h4>Language</h4>
                </div>
                <form method="get">
                    <div class="card-body">
                        <div class="form-group">
                            <select name="langid" class="form-control select1">
                                <option value=""></option>
                                <?php
                                $TEMPLANG = $LANG;
                                $langfiles = scandir($langdir);
                                $langhit = 0;
                                foreach ($langfiles as $key => $value) {
                                    if (strpos($value, '.lang.php') !== false) {
                                        include($langdir . '/' . $value);
                                        $isdeflang_sel = ($LANG['lang_iso'] == $langid) ? ' selected' : '';
                                        $isavallang_mark = ($langlistarr[$LANG['lang_iso']] != '') ? '+ ' : '- ';
                                        $isdeflang_mark = ($LANG['lang_iso'] == $cfgrow['langiso']) ? ' &#10003;' : '';
                                        echo "<option value='{$LANG['lang_iso']}'{$isdeflang_sel}>" . $isavallang_mark . $translation_str . $isdeflang_mark . "</option>";
                                        $langhit++;
                                    }
                                }
                                $LANG = $TEMPLANG;
                                $TEMPLANG = '';
                                if ($langhit < 1) {
                                    echo "<option disabled>No Record(s) Found!</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn btn-primary" onclick="location.href = 'index.php?hal=languagelist&langid=-'">
                                Create New
                            </button>
                            <button type="submit" value="Load" id="load" class="btn btn-info">
                                <i class="fa fa-fw fa-redo"></i> Load
                            </button>
                            <input type="hidden" name="hal" value="languagelist">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-8">	
            <div class="card">

                <form method="post" action="index.php" id="msgtplform">
                    <input type="hidden" name="hal" value="languagelist">

                    <div class="card-header">
                        <h4>Contents</h4>
                    </div>

                    <div class="card-body">
                        <p class="text-muted"><?php echo ($langid != '') ? '' : '<i class="fa fa-fw fa-long-arrow-alt-left"></i> Please select the Language from the drop down list on the left or click the <strong>Create New</strong> button to add a language based on English!'; ?></p>

                        <?php
                        if ($langid == '-') {
                            $langid = 'en';
                        }
                        $langfileload = $langdir . "/{$langid}.lang.php";
                        $langid = (file_exists($langfileload)) ? $langid : '';
                        if ($langid != '') {
                            $TEMPLANG = $LANG;
                            include($langfileload);
                            ?>

                            <div class="row">
                                <?php
                                if ($templangid == '-') {
                                    $translation_author = $translation_str = '';
                                    ?>
                                    <div class="form-group col-md-6">
                                        <label for="lang_iso">Language ISO 639-1 Code</label>
                                        <input type="text" name="lang_iso" id="lang_iso" class="form-control" value="" required>
                                        <div class="form-text text-muted"><em>Two-letter codes, <a href='https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes' target='_blank'>list of codes</a>.</em></div>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="form-group col-md-6">
                                        <label for="translation_stamp">ISO 639-1 Code / Time Stamp</label>
                                        <div>
                                            <strong><?php echo myvalidate($LANG['lang_iso']); ?></strong> / <?php echo myvalidate($translation_stamp); ?>
                                            <input type="hidden" name="lang_iso" value="<?php echo myvalidate($LANG['lang_iso']); ?>">
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                                <div class="form-group col-md-6">
                                    <label for="translation_author">Author</label>
                                    <input type="text" name="translation_author" id="translation_author" class="form-control" value="<?php echo myvalidate($translation_author); ?>" placeholder="<?php echo myvalidate($translation_author); ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="translation_str">Language</label>
                                    <input type="text" name="translation_str" id="translation_str" class="form-control" value="<?php echo myvalidate($translation_str); ?>" placeholder="<?php echo myvalidate($translation_str); ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="lang_charset">lang_charset</label>
                                    <input type="text" name="lang_charset" id="lang_charset" class="form-control" value="<?php echo myvalidate($LANG['lang_charset']); ?>" placeholder="<?php echo myvalidate($TEMPLANG['lang_charset']); ?>" required>
                                </div>
                            </div>

                            <blockquote>Language variable syntaxes:<br /><strong>g_</strong>[''] for general usage.<br /><strong>a_</strong>[''] for Admin CP and <strong>m_</strong>[''] for Member CP.</blockquote>

                            <div class="row">

                                <?php
                                foreach ($LANG as $key => $value) {
                                    if ($key == 'lang_iso' || $key == 'lang_charset') {
                                        continue;
                                    }
                                    ?>
                                    <div class="form-group col-md-6">
                                        <label for="<?php echo myvalidate($key); ?>"><?php echo myvalidate($key); ?></label>
                                        <input type="text" name="langval[<?php echo myvalidate($key); ?>]" id="<?php echo myvalidate($key); ?>" class="form-control" value="<?php echo myvalidate($value); ?>" placeholder="<?php echo myvalidate($TEMPLANG[$key]); ?>">
                                    </div>

                                    <?php
                                }

                                $isavallang_cek = ($langlistarr[$LANG['lang_iso']] != '') ? ' checked' : '';

                                $LANG = $TEMPLANG;
                                $TEMPLANG = '';
                                ?>

                            </div>

                            <div class="row">
                                <div class="form-group col-12">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="isavallang" value="1" class="custom-control-input" id="isavallang"<?php echo myvalidate($isavallang_cek); ?>>
                                        <label class="custom-control-label" for="isavallang">Make it available in the Language Dropdown List</label>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="langid" value="<?php echo myvalidate($langid); ?>">
                            <?php
                        }
                        ?>

                    </div>

                    <?php
                    if ($langid != '') {
                        $isdeflang_cek = ($templangid == $cfgrow['langiso']) ? ' checked' : '';
                        ?>
                        <div class="card-footer bg-whitesmoke text-md-right">
                            <div class="form-group float-left text-left">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="isdeflang" value="<?php echo myvalidate($templangid); ?>" class="custom-control-input" id="isdeflang"<?php echo myvalidate($isdeflang_cek); ?>>
                                    <label class="custom-control-label" for="isdeflang">Set as Default Language</label>
                                </div>
                            </div>

                            <button type="reset" name="reset" value="reset" id="reset" class="btn btn-warning">
                                <i class="fa fa-fw fa-undo"></i> Reset
                            </button>
                            <button type="submit" name="submit" value="submit" id="submit" class="btn btn-primary">
                                <i class="fa fa-fw fa-plus-circle"></i> Save Changes
                            </button>
                            <input type="hidden" name="dosubmit" value="1">
                        </div>
                        <?php
                    }
                    ?>

                </form>

            </div>
        </div>
    </div>
</div>
