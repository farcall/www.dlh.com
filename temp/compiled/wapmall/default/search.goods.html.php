<?php echo $this->fetch('header.html'); ?>    
<div class="mb-head">
    <a href="<?php echo url('app=default'); ?>" class="l_b">首页</a>
    <div class="tit">产品搜索</div>
    <a href="<?php echo url('app=category'); ?>" class="r_b">分类</a>
</div>

<script type="text/javascript" src="<?php echo $this->lib_base . "/" . 'search_goods.js'; ?>" charset="utf-8"></script>
<script>
    $(function () {
        $("*[ectype='wap_order_by'] a").click(function () {
            replaceParam('order', this.id + " desc");
            return false;
        });
    });
</script>



<style>
.search_goods_options {width: 100%;}
.search_goods_options ul {border-top: 1px solid #D7D6D6;border-bottom: 1px solid #d7d6d6;background-color: #fff;display: -webkit-box;}
.search_goods_options li {width: 25%;float: left;position: relative;}
.search_goods_options li a {display: block;background-color: #fff;text-align: center;line-height: 36px;padding-right: 16px;}
.search_goods_options li a.selected {background-color: #F63723;color: #fff;}
.search_goods_options li .decollator {width: 1px;color: #D7D6D6;position: absolute;top: 0px;right: 1px;height: 13px;}

.search_goods{background: #fff;}
.search_goods .list{font-size: 12px;display: table;width: 100%;}
.search_goods .list li{border-bottom: 1px solid #e5e5e5;margin-top: 10px;overflow: hidden;padding: 0 3% 10px;}
.search_goods .list li img{position: relative;margin-right: 2%;float: left;}
.search_goods .list li h2 {height: 36px;overflow: hidden;position: relative;font-size: 14px;font-weight: normal;line-height: 18px;}
.search_goods .list li p {line-height: 14px;position: relative;padding-right: 3%;margin-top:5px;}
.search_goods .list li p span {color: #e63b53;overflow: hidden;font-size: 14px;display: inline-block;}
.search_goods .list li p cite {font-size:12px;color: #a1a1a1;text-decoration: line-through;font-style: normal;float:right;}
</style>



<section class="search_goods_options" id="sort_order">
    <ul class="s-items" ectype='wap_order_by'>
        <li><a href="javascript:void(0);" <?php if ($_GET['order'] == ' sales desc '): ?>class="selected" <?php endif; ?>id="sales" >销量<span class="decollator">|</span></a></li>
        <li><a href="javascript:void(0);" id="price">价格<span class="decollator">|</span></a></li>
        <li><a href="javascript:void(0);" id="add_time">新品<span class="decollator">|</span></a></li>
        <li><a href="javascript:void(0);" id="comments">好评<span class="decollator">|</span></a></li>
    </ul>
</section>


<section class="search_goods">
    <?php if (! $this->_var['goods_list_order']): ?>
    <ul class="list">

        <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');$this->_foreach['fe_goods'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['fe_goods']['total'] > 0):
    foreach ($_from AS $this->_var['goods']):
        $this->_foreach['fe_goods']['iteration']++;
?>
        <a href="<?php echo url('app=goods&id=' . $this->_var['goods']['goods_id']. ''); ?>">
            <li>
                <img src="<?php echo $this->_var['goods']['default_image']; ?>" alt="<?php echo $this->_var['goods']['name']; ?>" width="80" height="68"/>
                <h2><?php echo htmlspecialchars($this->_var['goods']['goods_name']); ?></h2>
                <p><span><?php echo price_format($this->_var['goods']['price']); ?></span><cite><?php echo price_format($this->_var['goods']['market_price']); ?></cite></p>
            </li>
        </a>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </ul>
    <?php else: ?>
    <center style="font-size:16px;padding:20px 0">没有找到相关的商品！</center>
    <?php endif; ?>
</section>
<?php echo $this->fetch('page.bottom.html'); ?>


<?php echo $this->fetch('footer.html'); ?>