<?php echo $this->fetch('header.html'); ?>
<script type="text/javascript">
//<!CDATA[
$(function(){
    $('#partner_form').validate({
        errorPlacement: function(error, element){
            $(element).next('.field_notice').hide();
            $(element).after(error);
        },
        success       : function(label){
            label.addClass('right').text('OK!');
        },
        rules : {
            title : {
                required : true,
                byteRange: ['',100,'<?php echo $this->_var['charset']; ?>']
            },
            link  : {
                required : true,
                url      : true
            },
            logo  : {
                accept : 'png|jpe?g|gif'
            },
            sort_order : {
                number   : true
            }
        },
        messages : {
            title : {
                required : '请填写合作伙伴标题',
                byteRange: '标题不能超过100个字符'
            },
            link  : {
                required : '请填写合作伙伴的链接地址',
                url      : '这不是一个有效的链接地址'
            },
            logo  : {
                accept   : '只接受图片文件gif, jpg, jpeg, png'
            },
            sort_order  : {
                number   : '只能使用数字'
            }
        }
    });
});
//]]>
</script>
<div id="rightTop">
    <p>合作伙伴</p>
    <ul class="subnav">
        <li><a class="btn1" href="index.php?app=partner">管理</a></li>
        <?php if ($this->_var['partner']['partner_id']): ?>
        <li><a class="btn1" href="index.php?app=partner&amp;act=add">新增</a></li>
        <?php else: ?>
        <li><span>新增</span></li>
        <?php endif; ?>
    </ul>
</div>

<div class="info">
    <form method="post" enctype="multipart/form-data" id="partner_form">
        <table class="infoTable">
            <tr>
                <th class="paddingT15">
                    标题:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput2" id="partner_title" type="text" name="title" value="<?php echo htmlspecialchars($this->_var['partner']['title']); ?>" />
                    <label class="field_notice">合作伙伴的名称</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    链接:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableInput" id="partner_link" type="text" name="link" value="<?php echo htmlspecialchars($this->_var['partner']['link']); ?>" />
                    <label class="field_notice">合作伙伴的链接地址</label>
                </td>
            </tr>
            <tr>
                <th class="paddingT15">
                    图片标识:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="infoTableFile" id="partner_logo" type="file" name="logo" />
                    <label class="field_notice">合作伙伴的标志图片</label>
                </td>
            </tr>
            <?php if ($this->_var['partner']['logo']): ?>
            <tr>
                <th class="paddingT15">
                </th>
                <td class="paddingT15 wordSpacing5">
                <img src="<?php echo $this->_var['partner']['logo']; ?>" class="makesmall" max_width="120" max_height="90" />
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th class="paddingT15">
                    排序:</th>
                <td class="paddingT15 wordSpacing5">
                    <input class="sort_order" id="sort_order" type="text" name="sort_order" value="<?php echo $this->_var['partner']['sort_order']; ?>" />
                    <label class="field_notice">排序,只能是数字,值越大越靠后</label>
                </td>
            </tr>
        <tr>
            <th></th>
            <td class="ptb20">
                <input class="formbtn" type="submit" name="Submit" value="提交" />
                <input class="formbtn" type="reset" name="Submit2" value="重置" />
            </td>
        </tr>
        </table>
    </form>
</div>
<?php echo $this->fetch('footer.html'); ?>
