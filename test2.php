<!DOCTYPE html>
<html>
<head>
    <title>图片和视频上传</title>
</head>
<body>
<h1>图片和视频上传表单</h1>
<form action="upload.php" method="post" enctype="multipart/form-data">
    <label for="image">选择图片/视频：</label>
    <input type="file" id="image" name="image" accept="image/*,video/*"><br>

    <label for="video">选择视频：</label>
    <input type="file" id="video" name="video"><br>

    <input type="submit" value="上传">
</form>
</body>
</html>
