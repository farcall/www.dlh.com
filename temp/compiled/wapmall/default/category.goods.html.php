<?php echo $this->fetch('header.html'); ?>    
<div class="mb-head">
    <a href="javascript:history.back(-1)" class="l_b">返回</a>
    <div class="tit">分类浏览</div>
    <a href="javascript" class="r_b"></a>
</div>

<script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'kissy/build/kissy.js'; ?>"></script>
<script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'kissy/build/switchable/switchable-pkg.js'; ?>"></script>
<style>
    .box-category{display: -webkit-box;box-sizing: border-box;padding-top:0px;width: 100%;height: 100%;background: #f8f8f8;}
    .box-category .ks-switchable-nav{width: 24.6%;box-sizing: border-box;}
    .box-category .ks-switchable-nav li{position: relative;height: 48px;line-height: 48px;text-align: center;border-bottom: 1px solid #ddd;color: #333;font-size: 14px;border-left:3px solid rgba(0,0,0,0);width: 99%;overflow:hidden;}
    .box-category .ks-switchable-nav li.ks-active{border-left:3px solid #ff5000;color: #ff5000;background: #fff;}
    .box-category .ks-switchable-content{width: 75.4%;box-sizing: border-box;padding: 0 2% 0 2%;border-left: 1px solid #ddd;background: #fff;}
    .box-category .ks-switchable-content ul{}
    .box-category .ks-switchable-content ul li{width: 30%;margin-right: 8px;margin-top: 8px;overflow: hidden;background: #f8f8f8;float:left;}
    .box-category .ks-switchable-content ul li a {display: block;}
    .box-category .ks-switchable-content ul li img {background: #f8f8f8;width: 100%;display: block;}
    .box-category .ks-switchable-content ul li .name {text-align: center;font-size: 11px;color: #666;padding: 5px 0;}
</style>

<div class="box-category">
    <ul class="ks-switchable-nav">
        <?php $_from = $this->_var['gcategorys']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'gcategory');$this->_foreach['fe_gcategory'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['fe_gcategory']['total'] > 0):
    foreach ($_from AS $this->_var['gcategory']):
        $this->_foreach['fe_gcategory']['iteration']++;
?>
        <li <?php if (($this->_foreach['fe_gcategory']['iteration'] <= 1)): ?>class="ks-active"<?php endif; ?>><?php echo $this->_var['gcategory']['cate_name']; ?></li>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </ul>
    <div class="ks-switchable-content">
        <?php $_from = $this->_var['gcategorys']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'gcategory');$this->_foreach['fe_gcategory'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['fe_gcategory']['total'] > 0):
    foreach ($_from AS $this->_var['gcategory']):
        $this->_foreach['fe_gcategory']['iteration']++;
?>
        <ul <?php if (! ($this->_foreach['fe_gcategory']['iteration'] <= 1)): ?>style="display:none"<?php endif; ?>>
            <?php $_from = $this->_var['gcategory']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'child');if (count($_from)):
    foreach ($_from AS $this->_var['child']):
?>
            <a href="<?php echo url('app=search&cate_id=' . $this->_var['child']['cate_id']. ''); ?>">
                <li>
                    <div class="pic">
                        <img  src="<?php echo $this->_var['child']['cate_logo']; ?>" >
                    </div>
                    <p class="name"><?php echo $this->_var['child']['cate_name']; ?></p>
                </li>
            </a>
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </ul>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </div>
</div>
<script>
    KISSY.ready(function (S) {
        var tabs = new S.Tabs('.box-category', {
            aria: false
        });
    });
</script>
<?php echo $this->fetch('footer.html'); ?>