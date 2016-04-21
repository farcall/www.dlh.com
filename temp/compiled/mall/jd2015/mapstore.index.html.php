<?php echo $this->fetch('header.html'); ?>
<link type="text/css" href="<?php echo $this->res_base . "/" . 'css/mapstore.css'; ?>" rel="stylesheet" />
<script type="text/javascript" src="<?php echo $this->res_base . "/" . 'js/mapstore.js'; ?>" charset="utf-8"></script> 
<div id="store_scope" class="w-full">
    <div class="w mb20 mt10 clearfix">
        <div class="top_ad mb10">
            <img src="<?php echo $this->res_base . "/" . 'images/store_scope_ad.jpg'; ?>" >
        </div>

        <div class="left">
            <!--            
                        <div class="module">
                            <div class="mt">类别</div>
                            <div class="mc">
                                <ul ectype='ul_scategory'>
                                    <li <?php if (! $this->_var['scate_id']): ?>class="select"<?php endif; ?>><a href="javascript:void(0)" id="0"><i></i>全部</a></li>
                                    <?php $_from = $this->_var['scategorys']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'scategory');if (count($_from)):
    foreach ($_from AS $this->_var['scategory']):
?>
                                    <li <?php if ($this->_var['scategory']['cate_id'] == $this->_var['scate_id']): ?>class="select"<?php endif; ?>><a href="javascript:void(0)" id="<?php echo $this->_var['scategory']['cate_id']; ?>"><i></i><?php echo $this->_var['scategory']['cate_name']; ?></a></li>
                                     <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
                                </ul>
                            </div>
                        </div>-->


            <div class="module">
                <div class="mt">类别</div>
                <?php $_from = $this->_var['scategorys']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'scategory');$this->_foreach['fe_scategory'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['fe_scategory']['total'] > 0):
    foreach ($_from AS $this->_var['scategory']):
        $this->_foreach['fe_scategory']['iteration']++;
?>
                <div class="newitem <?php if ($this->_var['top_id'] == $this->_var['scategory']['id']): ?>hover<?php endif; ?>" ectype='ul_scategory'>
                    <h3>
                        <b></b>
                        <a href="javascript:void(0)" id="<?php echo $this->_var['scategory']['id']; ?>"><?php echo htmlspecialchars($this->_var['scategory']['value']); ?></a>
                    </h3>
                    <ul>
                        <?php if ($this->_var['scategory']['children']): ?>
                        <?php $_from = $this->_var['scategory']['children']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'child');if (count($_from)):
    foreach ($_from AS $this->_var['child']):
?>
                        <li><a href="javascript:void(0)" id="<?php echo $this->_var['child']['id']; ?>"><?php echo htmlspecialchars($this->_var['child']['value']); ?></a></li>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        <?php else: ?>
                        <li><a href="javascript:void(0)">暂无子分类</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </div>
        </div>

        <div class="right">
            <div class="filter">
                <div class="fl" ectype='order_by'>
                    <a href="javascript:void(0)" id="juli" <?php if ($_GET['order'] == 'juli' || ! $_GET['order']): ?>class="select"<?php endif; ?>>最近店铺</a> <span>|</span> 
                    <a href="javascript:void(0)" id="add_time" <?php if ($_GET['order'] == 'add_time'): ?>class="select"<?php endif; ?>>新开店铺</a> <span>|</span> 
                    <a href="javascript:void(0)" id="credit_value" <?php if ($_GET['order'] == 'credit_value'): ?>class="select"<?php endif; ?>>信誉店铺</a>
                </div>

                <div class="fr">
                    <a href="<?php echo url('app=mapstore&act=address'); ?>">更换地址</a>
                </div>

                <!--
                <div class="ser">
                    <i></i>
                    <input name="store_name" onfocus="if (value == '请输入店铺名称') {
                                value = ''
                            }" onblur="if (value == '') {
                                        value = '请输入店铺名称'
                                    }" value="请输入店铺名称" on="" id="store_name" type="text">
                    <b id="store_name_clear" style="display: none;"></b>
                </div>
                -->
            </div>

            <div class="list">
                <ul>
                    <?php $_from = $this->_var['stores']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'store');if (count($_from)):
    foreach ($_from AS $this->_var['store']):
?>
                    <li>
                        <i class="res"></i>
                        <!--
                        <i class="clo2"></i>
                        <i class="hot"></i>
                        <i class="new"></i>
                        -->
                        <a href="<?php echo url('app=store_meals&id=' . $this->_var['store']['store_id']. ''); ?>" target="_blank" class="img"><img src="<?php echo $this->_var['store']['store_logo']; ?>" width="70" height="70"/></a>
                        <a href="<?php echo url('app=store_meals&id=' . $this->_var['store']['store_id']. ''); ?>" target="_blank" class="title"><?php echo htmlspecialchars($this->_var['store']['store_name']); ?></a>
                        <a href="<?php echo url('app=store_meals&id=' . $this->_var['store']['store_id']. ''); ?>" target="_blank" class="goods_count">共计<span><?php echo htmlspecialchars($this->_var['store']['goods_count']); ?></span>商品</a>
                        <!--
                        <a href="<?php echo url('app=store_meals&id=' . $this->_var['store']['store_id']. ''); ?>" target="_blank" class="praise_rate">好评率<span><?php echo htmlspecialchars($this->_var['store']['praise_rate']); ?>%</span><img src="<?php echo $this->_var['store']['credit_image']; ?>" /></a>
                        -->
                        <a href="<?php echo url('app=store_meals&id=' . $this->_var['store']['store_id']. ''); ?>" target="_blank" class="juli">距离：<span><?php echo htmlspecialchars($this->_var['store']['juli']); ?>KM</span></a>
                        <span class="contact">
                            <?php if ($this->_var['store']['tel']): ?>电话：<?php echo htmlspecialchars($this->_var['store']['tel']); ?><?php endif; ?>
                            <?php if ($this->_var['store']['im_qq']): ?>
                            <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $this->_var['store']['im_qq']; ?>&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:<?php echo $this->_var['store']['im_qq']; ?>:52" alt="点击这里给我发消息" title="点击这里给我发消息"/></a>
                            <?php endif; ?>
                            <?php if ($this->_var['store']['im_ww']): ?>
                            <a target="_blank" href="http://amos.alicdn.com/msg.aw?v=2&uid=<?php echo $this->_var['store']['im_ww']; ?>&site=cnalichn&s=11&charset=UTF-8" ><img border="0" src="http://amos.alicdn.com/online.aw?v=2&uid=<?php echo $this->_var['store']['im_ww']; ?>&site=cnalichn&s=11&charset=UTF-8" alt="点击这里给我发消息" /></a>
                            <?php endif; ?>
                        </span>
                        <a href="<?php echo url('app=mapstore&act=view&id=' . $this->_var['store']['store_id']. ''); ?>" class="mapview" target="_blank">查看地图</a>
                    </li>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php echo $this->fetch('server.html'); ?>
<?php echo $this->fetch('footer.html'); ?>