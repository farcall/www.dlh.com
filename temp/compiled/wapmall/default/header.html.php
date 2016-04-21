<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
        <meta name="apple-mobile-web-app-capable" content="yes"/>
        <meta name="apple-touch-fullscreen" content="yes"/>
        <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
        <?php echo $this->_var['page_seo']; ?>
        <link href="<?php echo $this->res_base . "/" . 'css/global.css'; ?>" type="text/css" rel="stylesheet" />
        <link href="<?php echo $this->res_base . "/" . 'css/main.css'; ?>" type="text/css" rel="stylesheet" />
        <script type="text/javascript" src="index.php?act=jslang"></script>
        <script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'jquery.js'; ?>" charset="utf-8"></script>
        <script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'ecmall.js'; ?>" charset="utf-8"></script>
        <?php echo $this->_var['_head_tags']; ?>
        <script type="text/javascript">
            //<!CDATA[
            var SITE_URL = "<?php echo $this->_var['site_url']; ?>";
            var REAL_SITE_URL = "<?php echo $this->_var['real_site_url']; ?>";
            var PRICE_FORMAT = '<?php echo $this->_var['price_format']; ?>';
            //]]>
        </script>
    </head>

    <?php if ($this->_var['index']): ?>
    <header class="index-header">
        <div class="logo">
            <a href="index.php"><img src="<?php echo $this->_var['wap_site_logo']; ?>"/></a>
        </div>
        <div class="search">
            <div class="search-box">
                <form>
                    <input type="hidden" name="app" value="search"/>
                    <input type="hidden" name="act" value="index"/>
                    <input type="text" name="keyword"  value="" placeholder="搜索你关注的商品" class="keyword" />
                    <input type="submit" class="search-btn" value="" />
                </form>
            </div>
        </div>
        <div class="header_category">
            <a href="<?php echo url('app=category'); ?>"><span>分类</span></a>
        </div>
    </header>
    <?php endif; ?>