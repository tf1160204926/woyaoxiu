<?php
/**
 * 用户管理
 */

// 载入脚本
// ========================================

require '../functions.php';

// 访问控制
// ========================================

// 获取登录用户信息
xiu_get_current_user();

// 处理表单提交
// ========================================

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // 表单校验
  if (empty($_POST['email']) || empty($_POST['slug']) || empty($_POST['nickname'])) {
    // 表单不合法，提示错误信息（可以分开判断，提示更加具体的信息）
    $message = '完整填写表单内容';
  } else if (empty($_POST['id'])) {
    // 表单合法，数据持久化（通俗说法就是保存数据）
    // 没有提交 ID 代表新增，则新增数据
    $sql = sprintf("insert into users values (null, '%s', '%s', '%s', '%s', null, null, 'unactivated')",
      $_POST['slug'],
      $_POST['email'],
      $_POST['password'],
      $_POST['nickname']
    );
    // 响应结果
    $message = xiu_execute($sql) > 0 ? '保存成功' : '保存失败';
  } else {
    // 提交 ID 就代表是更新，则更新数据
    $sql = sprintf("update users set slug = '%s', email = '%s', nickname = '%s' where id = %d",
      $_POST['slug'],
      $_POST['email'],
      $_POST['nickname'],
      $_POST['id']
    );
    // 响应结果
    $message = xiu_execute($sql) > 0 ? '保存成功' : '保存失败';
  }
}

// 查询数据
// ========================================

// 查询全部用户信息
$users = xiu_query('select * from users');

// 数据过滤函数
// ========================================

/**
 * 将英文状态描述转换为中文
 * @param  string $status 英文状态
 * @return string         中文状态
 */
function convert_status ($status) {
  switch ($status) {
    case 'unactivated':
      return '未激活';
    case 'activated':
      return '已激活';
    case 'forbidden':
      return '禁止';
    case 'trashed':
      return '回收站';
    default:
      return '未知';
  }
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>用户</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
    <nav class="navbar">
      <button class="btn btn-default navbar-btn fa fa-bars"></button>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="profile.php"><i class="fa fa-user"></i>个人中心</a></li>
        <li><a href="logout.php"><i class="fa fa-sign-out"></i>退出</a></li>
      </ul>
    </nav>
    <div class="container-fluid">
      <div class="page-title">
        <h1>用户</h1>
      </div>
      <?php if (isset($message)) : ?>
      <div class="alert alert-<?php echo $message == '保存成功' ? 'success' : 'danger'; ?>">
        <strong><?php echo $message == '保存成功' ? '成功' : '错误'; ?>！</strong><?php echo $message; ?>
      </div>
      <?php endif; ?>
      <div class="row">
        <div class="col-md-4">
          <form action="/admin/users.php" method="post">
            <h2>添加新用户</h2>
            <input id="id" name="id" type="hidden">
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary btn-save" type="submit">添加</button>
              <button class="btn btn-default btn-cancel" type="button" style="display: none;">取消</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm btn-delete" href="/admin/user-delete.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $item) { ?>
              <tr data-id="<?php echo $item['id']; ?>">
                <td class="text-center"><input type="checkbox"></td>
                <td class="text-center"><img class="avatar" src="<?php echo empty($item['avatar']) ? '/static/assets/img/default.png' : $item['avatar']; ?>"></td>
                <td><?php echo $item['email']; ?></td>
                <td><?php echo $item['slug']; ?></td>
                <td><?php echo $item['nickname']; ?></td>
                <td><?php echo convert_status($item['status']); ?></td>
                <td class="text-center">
                  <a href="javascript:;" class="btn btn-default btn-xs btn-edit">编辑</a>
                  <a href="/admin/user-delete.php?id=<?php echo $item['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'users'; ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function () {
      // 获取所需操作的界面元素
      var $btnDelete = $('.btn-delete')
      var $thCheckbox = $('th > input[type=checkbox]')
      var $tdCheckbox = $('td > input[type=checkbox]')

      // 用于记录界面上选中行的数据 ID
      var checked = []

      /**
       * 表格中的复选框选中发生改变时控制删除按钮的链接参数和显示状态
       */
      $tdCheckbox.on('change', function () {
        var $this = $(this)

        // 为了可以在这里获取到当前行对应的数据 ID
        // 在服务端渲染 HTML 时，给每一个 tr 添加 data-id 属性，记录数据 ID
        // 这里通过 data-id 属性获取到对应的数据 ID
        var id = parseInt($this.parent().parent().data('id'))

        // ID 如果不合理就忽略
        if (!id) return

        if ($this.prop('checked')) {
          // 选中就追加到数组中
          checked.push(id)
        } else {
          // 未选中就从数组中移除
          checked.splice(checked.indexOf(id), 1)
        }

        // 有选中就显示操作按钮，没选中就隐藏
        checked.length ? $btnDelete.fadeIn() : $btnDelete.fadeOut()

        // 批量删除按钮链接参数
        // search 是 DOM 标准属性，用于设置或获取到的是 a 链接的查询字符串
        $btnDelete.prop('search', '?id=' + checked.join(','))
      })

      /**
       * 全选 / 全不选
       */
      $thCheckbox.on('change', function () {
        var checked = $(this).prop('checked')
        // 设置每一行的选中状态并触发 上面 👆 的事件
        $tdCheckbox.prop('checked', checked).trigger('change')
      })

      /**
       * slug 预览
       */
      $('#slug').on('input', function () {
        $(this).next().children().text($(this).val())
      })

      /**
       * 编辑分类
       */
      $('.btn-edit').on('click', function () {
        // 变量本地化（效率）
        var $tr = $(this).parent().parent()
        var $tds = $tr.children()

        // 拿到当前行数据
        var id = $tr.data('id')
        var email = $tds.eq(2).text()
        var slug = $tds.eq(3).text()
        var nickname = $tds.eq(4).text()

        // 将数据放到表单中
        $('#id').val(id)
        $('#email').val(email)
        $('#slug').val(slug).trigger('input')
        $('#nickname').val(nickname)
        $('#password').parent().hide()

        // 界面显示变化
        $('form > h2').text('编辑用户')
        $('form > div > .btn-save').text('保存')
        $('form > div > .btn-cancel').show()
      })

      /**
       * 取消编辑
       */
      $('.btn-cancel').on('click', function () {
        // 清空表单元素上的数据
        $('#id').val('')
        $('#email').val('')
        $('#slug').val('').trigger('input')
        $('#nickname').val('')
        $('#password').parent().show()

        // 界面显示变化
        $('form > h2').text('添加新用户')
        $('form > div > .btn-save').text('添加')
        $('form > div > .btn-cancel').hide()
      })
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
