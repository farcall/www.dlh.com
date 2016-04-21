<?php echo $this->fetch('member.header.html'); ?>
<div class="content">
    <?php echo $this->fetch('member.menu.html'); ?>
    <div id="right">
        <?php echo $this->fetch('my_statistics.select.html'); ?>
        <?php echo $this->fetch('member.submenu.html'); ?>
        <style>
            table {BORDER-RIGHT: #cedced 1px solid;border-left: 1px solid #CEDCED;BORDER-TOP: #cedced 1px solid;MARGIN: 0.6em auto;WIDTH: 100%;BORDER-BOTTOM: #cedced 1px solid;BORDER-COLLAPSE: collapse;}
            TD {PADDING-RIGHT: 0.9em; PADDING-LEFT: 0.9em; PADDING-BOTTOM: 0.3em; BORDER-right: #cedced 1px solid; COLOR: #000; PADDING-TOP: 0.3em; BORDER-BOTTOM: #cedced 1px solid; TEXT-ALIGN: left}
            TH {PADDING-RIGHT: 10px; PADDING-LEFT: 10px; FONT-WEIGHT: normal; PADDING-BOTTOM: 0.3em; BORDER-right: #cedced 1px solid; COLOR: #5e759d; PADDING-TOP: 0.3em; BORDER-BOTTOM: #cedced 1px solid; TEXT-ALIGN: left}
            THEAD TH {FONT-WEIGHT: lighter; FONT-SIZE: 12px; BACKGROUND: #e9f1fa; COLOR: #638199; TEXT-ALIGN: left}
            .th1 {FONT-WEIGHT: bold; FONT-SIZE: 14px; COLOR: #333333; BORDER-BOTTOM: #cedced 2px solid; BACKGROUND-COLOR: #fff; TEXT-ALIGN: left}
            .odd TH {FONT-WEIGHT: bold; COLOR: #282828; TEXT-ALIGN: left}
            .odd .all_left {TEXT-ALIGN: left}
        </style>
        <table width="806">
            <thead>
                <tr class="odd">
                    <th width="18%" class="all_left"><strong>日期</strong></th>
                    <th width="11%"><strong>PV</strong></th>
                    <th width="24%"><strong>独立访客(UV)</strong></th>
                    <th width="22%"><strong>IP</strong></th>
                    <th width="25%" class="all_left"><strong>浏览量</strong></th>
                </tr>	
            </thead>
            <tbody>
                <tr bgcolor="">
                    <td><?php echo $this->_var['lang'][$this->_var['_curmenu']]; ?></td>
                    <td class="num1"><?php echo $this->_var['statistics']['total']['pv']; ?></td>
                    <td class="num1"><?php echo $this->_var['statistics']['total']['uv']; ?></td>
                    <td class="num1"><?php echo $this->_var['statistics']['total']['ip']; ?></td>
                    <td class="all_left"> <?php echo $this->_var['statistics']['total']['visit_times']; ?></td>
                </tr>
            </tbody>
        </table>

        <div style="height:290px;">
            <div id="my_chart"></div>
        </div>
        <div style="text-align:center;padding-top: 10px;padding-bottom: 10px;">
            <input title="页面浏览量（Page View），访客每打开一次页面记录为 1 个PV。" type="radio" checked="" name="item" value="pv" onclick="radio_click(this)">
            <label title="页面浏览量（Page View），访客每打开一次页面记录为 1 个PV。" style="font-weight:bold;color:#0033CC">浏览量(PV)</label>
            <input title="独立访客数（Unique Visitor），1 天内相同访问多次访问您的店铺记录为 1 个UV。" type="radio"  name="item" value="uv" onclick="radio_click(this)">
            <label title="独立访客数（Unique Visitor），1 天内相同访问多次访问您的店铺记录为 1 个UV。" style="font-weight:bold;color:#33CC33" >独立访客(UV)</label>
            <input title="访客访问次数（Visit View），从访客来到网站到最终离开网站的所有页面，记录为 1 次访问；若访问超过 30 分钟，则计算为本次访问结束。" type="radio"  name="item" value="visit_times" onclick="radio_click(this)">
            <label title="访客访问次数（Visit View），从访客来到网站到最终离开网站的所有页面，记录为 1 次访问；若访问超过 30 分钟，则计算为本次访问结束。" style="font-weight:bold;color:#FF0000">访问次数(VV)</label>
            <input title="1 天内相同的IP地址访问，记录为 1 个IP。" type="radio"  name="item" value="ip" onclick="radio_click(this)">
            <label title="1 天内相同的IP地址访问，记录为 1 个IP。" style="font-weight:bold;color:#ffa902">独立IP</label>
        </div>

        <div class="tabdiv1">
            <table width="770">
                <thead>
                    <tr class="odd">
                        <th width="18%" class="all_left"><strong>时间段</strong></th>
                        <th width="11%"><strong>浏览量(PV)</strong></th>
                        <th width="24%"><strong>独立访客(UV)</strong></th>
                        <th width="22%"><strong>独立IP</strong></th>
                        <th width="25%" class="all_left"><strong>访问次数(VV)</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $_from = $this->_var['statistics']['time']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'time');$this->_foreach['f_time'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['f_time']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['time']):
        $this->_foreach['f_time']['iteration']++;
?>
                    <tr>
                        <td><?php echo $this->_var['key']; ?></td>
                        <td class="num1"><?php if ($this->_var['time']['pv']): ?><?php echo $this->_var['time']['pv']; ?><?php else: ?>0<?php endif; ?></td>
                        <td class="num1"><?php if ($this->_var['time']['uv']): ?><?php echo $this->_var['time']['uv']; ?><?php else: ?>0<?php endif; ?></td>
                        <td class="num1"><?php if ($this->_var['time']['ip']): ?><?php echo $this->_var['time']['ip']; ?><?php else: ?>0<?php endif; ?></td>
                        <td> <?php if ($this->_var['time']['visit_times']): ?><?php echo $this->_var['time']['visit_times']; ?><?php else: ?>0<?php endif; ?></td>
                    </tr>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    <tr>
                        <td>共计</td>
                        <td class="num1"><?php echo $this->_var['statistics']['total']['pv']; ?></td>
                        <td class="num1"><?php echo $this->_var['statistics']['total']['uv']; ?></td>
                        <td class="num1"><?php echo $this->_var['statistics']['total']['ip']; ?></td>
                        <td><?php echo $this->_var['statistics']['total']['visit_times']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <script type="text/javascript" src="includes/open-flash-chart-2/js/swfobject.js"></script>
        <script type="text/javascript">
            function getQueryString(name) {
                var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
                var r = window.location.search.substr(1).match(reg);
                if (r != null) return unescape(r[2]); return null;
            }
            
            var st = getQueryString("st");
            var et = getQueryString("et")
            var data_url = "index.php?app=my_statistics&act=solid_dot&st="+st+"&et="+et;
            data_url = data_url.replace(/&/g,"%26");
    
            swfobject.embedSWF(
            "includes/open-flash-chart-2/open-flash-chart.swf", "my_chart", "806", "290",
            "9.0.0", "expressInstall.swf",
            {"data-file":data_url});
            
            
            function radio_click(obj){
                var data_type=obj.value;
                var st = getQueryString("st");
                var et = getQueryString("et")
                var data_url = "index.php?app=my_statistics&act=solid_dot&data_type="+data_type+"&st="+st+"&et="+et;
                data_url = data_url.replace(/&/g,"%26");
    
                swfobject.embedSWF(
                "includes/open-flash-chart-2/open-flash-chart.swf", "my_chart", "806", "290",
                "9.0.0", "expressInstall.swf",
                {"data-file":data_url});
            }
            
        </script>
    </div>
    <div class="clear"></div>
</div>
<?php echo $this->fetch('footer.html'); ?>