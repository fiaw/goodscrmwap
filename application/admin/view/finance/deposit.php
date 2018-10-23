<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header"}
</head>
<body>
{// 引入顶部导航文件}
{include file="public/topbar"}

<div class="viewFramework-body viewFramework-sidebar-full">
    {// 引入左侧导航文件}
    {include file="public/sidebar"}
    <!-- 主体内容 开始 -->
    <div class="viewFramework-product viewFramework-product-col-1">
        <!-- 中间导航 开始 viewFramework-product-col-1-->
        {include file="public/product-navbar-finance"}
        <!-- 中间导航 结束 -->
        <div class="viewFramework-product-body">
            <div class="console-container">
                <!--内容开始-->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="console-title console-title-border clearfix">
                            <div class="pull-left">
                                <h5><span>{$title}</span></h5>
                            </div>
                            <div class="pull-right">
                                <a href="javascript:window.location.reload();" class="btn btn-default">
                                    <span class="glyphicon glyphicon-refresh"></span>
                                    <span>刷新</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-inline marginTop10">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="control-label" for="projectNameInput">搜索条件 :</label>
                                <input name="projectNameInput" id="projectNameInput" class="ipt form-control" data-toggle="tooltip" data-placement="top" title="单号 / 公司名称 / 客户名称">
                                <button type="button" class="btn btn-primary" id="searchprojectName">搜索</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-hover syc-table">
                            <thead>
                            <tr>
                                <th>销售单号</th>
                                <th>公司名称</th>
                                <th>客户名称</th>
                                <th>订单数量</th>
                                <th>订单优惠</th>
                                <th>订单金额</th>
                                <th>订单确认</th>
                                <th>订单状况</th>
                                <th>销售日期</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            {volist name="list" id="vo" empty="$empty"}
                            <tr>
                                <td>{$vo.pnumber}</td>
                                <td>{$vo.pbname}</td>
                                <td>{$vo.pcsname}</td>
                                <td>{$vo.pcount}</td>
                                <td>{$vo.pyouhui}</td>
                                <td>￥{$vo.pamount|number_format=2}</td>
                                <td>
                                    {eq name="$vo.affirm" value="0"}
                                    <span class="label label-sm label-default">未确认</span>
                                    {else/}
                                    <span class="label label-sm label-success">已确认</span>
                                    {/eq}
                                </td>
                                <td>{:purchase_status($vo.status)}</td>
                                <td>{$vo.pstart_date}</td>
                                <td><a href="{:Url('orders/view',['pid'=>$vo.pnumber])}" target="_blank">查看</a>
                                </td>
                            </tr>
                            {/volist}
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="10">
                                    <div class="pull-left">
                                        </button>
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
        </div>
    </div>
</div>

{// 引入底部公共JS文件}
{include file="public/footer"}
<script type="text/javascript">
    $(document).ready(function() {
        // 当前页面分类高亮
        $("#sidebar-finance").addClass("sidebar-nav-active"); // 大分类
        $("#finance-receivables").addClass("active"); // 小分类
        $("#product-finance-deposit").addClass("active"); // 小分类

        $('[data-toggle="tooltip"]').tooltip(); //工具提示

        // 单号搜索
        $("#searchprojectName").on('click keyup', function () {
            var NameInput = $("input[name='projectNameInput']").val();
            //var patrn = /^\+?[0-9]*$/;　　//判断是否为正整数 patrn.exec(NameInput) == null
            if (NameInput.length < '2' || NameInput.length > '16') {
                toastr.warning('请输2-16个字符');
                return false
            } else {
                window.location.href="{:Url('finance/deposit',['m'=>'pnumber'])}?k="+NameInput;
            }
        });
    });
</script>
</body>
</html>