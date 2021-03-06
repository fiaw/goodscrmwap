{extend name="public/base"}
{block name="header"}

{/block}

{block name="main"}
            <div class="container-fluid">
                <!--内容开始-->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="console-title console-title-border clearfix">
                            <div class="pull-left">
                                <h5><span>{$title}</span></h5>
                            </div>
                            <div class="pull-right">
                                <a class="btn btn-primary" href="{:url('newcreate')}">新建</a>
                                <a href="javascript:window.location.reload();" class="btn btn-default">
                                    <span class="glyphicon glyphicon-refresh"></span>
                                    <span>刷新</span></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-inline marginTop10">
                        <div class="col-lg-12">
                        
                        	<form action="" method="get">
                        
                            <div class="form-group">
                                <label class="control-label" for="cus_name">客户名称 :</label>
                                <input name="cus_name" id="cus_name" class="ipt form-control" value="<?php if(isset($_GET['cus_name'])){echo $_GET['cus_name'];}?>" data-toggle="tooltip" data-placement="top" title="客户名称">
                                
                            </div>
                            	<div class="form-group">
                                    <label class="control-label" for="start_time">开票日期 :</label>
                                    <input name="start_time" id="start_time" <?php if (isset($_GET['start_time'])):?>value="<?php echo $_GET['start_time'];?>"<?php endif;?> class="ipt form-control">
                                    <span>到</span>
                                </div>
                                
                                <div class="form-group">
                                    <input name="end_time" id="end_time" <?php if (isset($_GET['end_time'])):?>value="<?php echo $_GET['end_time'];?>"<?php endif;?> class="ipt form-control">
                                </div>
                                
                                <div class="form-group">
                                	<label class="control-label" for="open_status">开票状态 :</label>
                                	<select name="open_status" id="open_status" class="form-control">
                                		<option value="">全部</option>
                                		<option value="0" <?php if (isset($_GET['open_status']) && $_GET['open_status'] != '' && $_GET['open_status'] == 0){echo 'selected="selected"';}?>>未开票</option>
                                		<option value="1" <?php if (isset($_GET['open_status']) && $_GET['open_status'] == 1){echo 'selected="selected"';}?>>已开票</option>
                                	</select>
                                </div>
                                <div class="form-group">
                                	<label class="control-label" for="invoice_status">对账状态 :</label>
                                	<select name="invoice_status" id="invoice_status" class="form-control">
                                		<option value="">全部</option>
                                		<option value="1" <?php if (isset($_GET['invoice_status']) && $_GET['invoice_status'] == 1){echo 'selected="selected"';}?>>已创建</option>
                                		<option value="9" <?php if (isset($_GET['invoice_status']) && $_GET['invoice_status'] == 9){echo 'selected="selected"';}?>>已确认</option>
                                		<option value="2" <?php if (isset($_GET['invoice_status']) && $_GET['invoice_status'] == 2){echo 'selected="selected"';}?>>已核销</option>
                                		<option value="0" <?php if (isset($_GET['invoice_status']) && $_GET['invoice_status'] != '' && $_GET['invoice_status'] == 0){echo 'selected="selected"';}?>>已取消</option>
                                	</select>
                                </div>
                                <button type="submit" class="btn btn-primary">查找</button>
                                </form>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-hover syc-table border">
                            <thead>
                            <tr>
							
							    <th>对账单号</th>
                                <th>对账日期</th>
                                <th>客户名称</th>
                                <th>金额</th>
                                <th>开票金额</th>
                                <th>冲减金额</th>
                                <th>是否开票</th>
                                <!--<th>出货单号</th>
                                <th>销售/订单号</th>-->
								<th>对账状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            {volist name="list" id="vo" empty="$empty"}
                                <tr>
								
								<td>{$vo.invoice_sn}</td>
                                <td>{$vo.invoice_date}</td>
                                <td>{$vo.cus_name}</td>
                                <td>{$vo.total_money}</td>
                                <td>{$vo.pay_money}</td>
                                <td>{$vo.diff_money}</td>
                                <td>{if condition="$vo['is_open']"}已开票{else}未开票{/if}</td>
								<td>
                                {if condition="$vo['is_confirm']"}
                                	{if condition="$vo['status']==2"}已核销{else}已确认{/if}
                                {else}
                                	{if condition="$vo['status']==1"}已创建{elseif condition="$vo['status']==2"}已核销{else}已取消{/if}
                                {/if}
                                </td>
                                <td>
                                	<a href="{:url('info',['id' => $vo['id']])}">详情</a>
                                	{if condition="$vo['status']!=0"}
                                		{if condition="$vo['is_confirm']"}
                                			<span class="text-explode">|</span>
                                			<a href="{:url('ticketrecrod',['id' => $vo['id']])}">发票记录</a>
	                                		
	                                		<span class="text-explode">|</span>
	                                		<a href="javascript:;" onclick="_open({$vo['id']})">开发票</a>
<!-- 
                                    		<span class="text-explode">|</span>
                                    		<a href="javascript:;" onclick="is_open({$vo['id']})">已开票</a>
-->
	                                		
	                                		{if condition="$vo['status']==1"}
	                                    	<!--<span class="text-explode">|</span>
	                                    	<a href="javascript:;" onclick="_status({$vo['id']})">已核销</a>-->
	                                    	{/if}
                                    	{else}
	                                    	<!--<span class="text-explode">|</span>
	                                    	<a href="javascript:;" onclick="_confirm({$vo['id']})">确认</a>-->
                                    	{/if}
                                		{if condition="$vo['status']==1 and $vo['is_confirm']==0"}
                                		<span class="text-explode">|</span>
                                		<a href="javascript:;" onclick="_close({$vo['id']})">取消</a>
                                		{/if}
                                	{/if}
                                	{if condition="!$vo['is_confirm'] && $vo['status']"}
                                	<span class="text-explode">|</span>
                                	<a href="{:url('edit',['id' => $vo['id']])}">编辑</a>
                                	{/if}
                                	<!-- 
                                	<span class="text-explode">|</span>
                                	<a href="javascript:;" onclick="deleteOrdersOne({$vo['id']})">删除</a>
                                	-->
                                </td>
                                </tr>
                            {/volist}
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="20">
                                    <div class="pull-left">
                                        
                                    </div>
                                    <div class="pull-right page-box">{$page}</div>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!--内容结束-->
            </div>
{/block}
{block name="footer"}
<script type="text/javascript">
    $(document).ready(function() {
        // 当前页面分类高亮
        $("#sidebar-account").addClass("sidebar-nav-active"); // 大分类
        $("#account-index").addClass("active"); // 小分类
        layui.use('laydate', function() {
            var laydate = layui.laydate;
            //日期选择器
            laydate.render({
                elem: '#start_time'
                //,type: 'date' //默认，可不填
            });
            laydate.render({
                elem: '#end_time'
                //,type: 'date' //默认，可不填
            });
        });
        $('[data-toggle="tooltip"]').tooltip(); //工具提示

        // 使用prop实现全选和反选
        $("#ckSelectAll").on('click', function () {
            $("input[name='ckbox[]']").prop("checked", $(this).prop("checked"));
        });
        // 获取选中元素
        $("#DelAllAttr").on('click', function () {
            layui.use(['layer'], function() {
                var layer = layui.layer;
                layer.open({
                    title: '温馨提示',
                    content: '是否要废除所有选择的订单？',
                    btn: ['我已确认', '放弃操作'],
                    yes: function(index, layero){
                        layer.close(index);
                        var valArr = new Array;
                        $("input[name='ckbox[]']:checked").each(function(i){
                            valArr[i] = $(this).val();
                        });
                        if (valArr.length !== 0 && valArr !== null && valArr !== '') {
                            var data={name:'delallattr',uid:valArr.join(',')};
                            $.sycToAjax("{:Url('orders/scrap')}", data);
                        };
                        return false;
                    }
                    ,btn2: function(index, layero){
                        layer.close(index);
                    }
                });
            });
        });
    });
    //单条订单操作
    function is_open(e) {
        if(confirm("确认操作？")){
            if (!isNaN(e) && e !== null && e !== '') {
                var data={name:'scrap',id:e};
                $.sycToAjax("{:url('open')}", data);
            }
        };
        return false;
    }
    function _open(e) {
        //if(confirm("确认操作？")){
            if (!isNaN(e) && e !== null && e !== '') {
                var data={name:'scrap',id:e};
                //$.sycToAjax("{:url('open')}", data);
            	var title = '新增发票记录';
                bDialog.open({
                    title : title,
                    height: 450,
                    width:410,
                    url : '{:url(\'openticket\')}?id='+e,
                    callback:function(data){
                        if(data && data.results && data.results.length > 0 ) {
                            window.location.reload();
                        }
                    }
                });
            }
        //};
        return false;
    }
    function _status(e) {
        if(confirm("确认操作？")){
            if (!isNaN(e) && e !== null && e !== '') {
                var data={name:'scrap',id:e};
                $.sycToAjax("{:url('status')}", data);
            }
        };
        return false;
    }
    function _confirm(e) {
        if(confirm("确认操作？")){
            if (!isNaN(e) && e !== null && e !== '') {
                var data={name:'scrap',id:e};
                $.sycToAjax("{:url('confirm')}", data);
            }
        };
        return false;
    }
    function _close(e) {
        if(confirm("确认操作？")){
            if (!isNaN(e) && e !== null && e !== '') {
                var data={name:'scrap',id:e};
                $.sycToAjax("{:url('close')}", data);
            }
        };
        return false;
    }
    //单条恢复订单操作
    function huifuLogisticsOne(e) {
        if(confirm("确定恢复此订单？")){
            if (!isNaN(e) && e !== null && e !== '') {
                var data={name:'scrap',pid:e};
                $.sycToAjax("{:url('orders/huifu')}", data);
            }
        };
        return false;
    }

    //单条删除订单操作
    function deleteOrdersOne(e) {
        if(confirm("确定删除？")){
            if (!isNaN(e) && e !== null && e !== '') {
                var data={name:'scrap',id:e};
                $.sycToAjax("{:url('delete')}", data);
            }
        };
        return false;
    }
</script>
{/block}