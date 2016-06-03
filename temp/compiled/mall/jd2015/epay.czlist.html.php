<?php echo $this->fetch('member.header.html'); ?>
<script language = "JavaScript">
    $(function() {
        /* 预存款充值 */
        $('*[ectype="recharge-method"] input[name="method"]').click(function() {
            $('*[ectype="online"]').hide();
            $('*[ectype="offline"]').hide();
            $('*[ectype="' + $(this).val() + '"]').show();
        })
    });
    function online_chongzhi()
    {
        if (document.online_form.cz_money.value == "")
        {
            alert("填写要充值的金额");
            document.online_form.cz_money.focus();
            return false;
        }
        return true;
    }
</script>
<div class="content">
    <?php echo $this->fetch('member.menu.html'); ?>
    <div id="right">
        <?php echo $this->fetch('member.submenu.html'); ?>

        <div class="wrap">
            <div class="public table epay">
                <div class="notice-word">将您的账户进行充值。</div>
                <div class="title clearfix">
                    <h2 class="float-left">账户充值</h2>
                    <p class="float-left">余额：<strong><?php echo $this->_var['epay']['money']; ?></strong> 元</p>
                    <div class="float-right link">
                        <a  href="<?php echo url('app=epay&act=logall&type=60'); ?>">充值记录</a>
                    </div>
                </div>
                <div class="form czlist">
                    <dl class="clearfix">
                        <dt>充值方式：</dt>
                        <dd class="clearfix" ectype="recharge-method">
                            <div class="czlist_type">
                                <input name="method" type="radio" value="offline" id="offline"/><label for="offline">线下汇款</label>
                            </div>
                        </dd>
                        <img src="/data/files/offline/huikuan.jpg" alt=""/>
                    </dl>

                    <form name="offline_form" action="index.php?app=epay&act=offline_chongzhi" method="post" ectype="offline" style="display: none;">
                        <dl class="clearfix">
                            <dt>汇款说明：</dt>
                            <dd class="clearfix" style="line-height:25px;font-size:13px;">
                                <?php echo $this->_var['epay_offline_info']; ?>
                            </dd>
                        </dl>
                        <dl class="clearfix">
                            <dt>汇款信息：</dt>
                            <dd><textarea name="message" cols="50" rows="5"></textarea>
                                <br/>汇款银行,流水号，汇款时间，汇款金额
                            </dd>
                        </dl>
                        <dl class="clearfix">
                            <dt>汇款人姓名：</dt>
                            <dd>
                                <input name="realname" value=""/>
                            </dd>
                        </dl>
                        <dl class="clearfix">
                            <dt>汇款人电话：</dt>
                            <dd><input name="mobile" value=""/></dd>
                        </dl>
                        <dl class="clearfix">
                            <dt>&nbsp;</dt>
                            <dd class="submit">
                                <span class="epay_btn">
                                    <input type="submit" value="提交" />
                                </span>
                            </dd>
                        </dl>
                    </form>












                </div>
            </div>
        </div>


        	
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<div class="clear"></div>
<?php echo $this->fetch('footer.html'); ?>
