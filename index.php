<?php
ini_set("error_reporting", E_ALL);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>サイトタイトル</title>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="Library/jRange/jquery.range.css">
    <script src="Library/jRange/jquery.range-min.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <title>slider demo</title>
    <style>
        .length_slider + .slider-container {
            margin-top: 15px;
        }
        .height_slider + .slider-container {
            margin-top: 15px;
        }
        .form {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div id="search_slider_area" style="margin-bottom: 15px;">
    <div class="form">
        <label for="length">長さ</label>
        <input id="length" type="hidden" value="0,100" class="length_slider">
    </div>
    <div class="form">
        <label for="height">高さ</label>
        <input id="height" type="hidden" value="0,100" class="height_slider">
    </div>
</div>
<input class="email" id="t_i_input" type="email" name="email" placeholder="test@test.co.jp" style="font-size:17px;" value="<?php echo 'a'; ?>">
<div id="search_result_area">

</div>

<script>
    const ranges = {
        length_slider: $('.length_slider').jRange({
            from: 0,
            to: 100,
            step: 1,
            scale: [0,100],
            format: '%s',
            width: 300,
            showLabels: true,
            snap: true,
            isRange: true,
            ondragend: function (value) {
                let validate = fromToValidate(value);
                if (!validate) {
                    alert('不正な値です。');
                    return false;
                }
                let fromTo = getFromTo(value);
                let formData = new FormData();
                formData.append('range_type', '1');
                formData.append('from', fromTo.from);
                formData.append('to', fromTo.to);
                doAxios(formData);
            }
        }),
        height_slider: $('.height_slider').jRange({
            from: 0,
            to: 100,
            step: 1,
            scale: [0,100],
            format: '%s',
            width: 300,
            showLabels: true,
            snap: true,
            isRange: true,
            ondragend: function (value) {
                let validate = fromToValidate(value);
                if (!validate) {
                    alert('不正な値です。');
                    return false;
                }
                let fromTo = getFromTo(value);
                let formData = new FormData();
                formData.append('range_type', '2');
                formData.append('from', fromTo.from);
                formData.append('to', fromTo.to);
                doAxios(formData);
            }
        }),
    }

    function doAxios (formData) {
        axios.post('./post.php', formData, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        }).then(function (response) {
            let target_element = document.getElementById('search_result_area');
            while (target_element.lastChild) {
                target_element.removeChild(target_element.lastChild);
            }
            if (response.data.items.length === 0) {
                return false;
            }
            Object.keys(response.data.items).forEach(function (name) {
                let length = response.data.items[name].length;
                let height = response.data.items[name].height;
                let element = resultHtml(name, length, height);
                target_element.append(element);
            });
            Object.keys(ranges).forEach(function (form_class_name, index) {
                let search_keyword = 'length';
                if (index === 1) {
                    search_keyword = 'height';
                }
                let min = response.data[search_keyword].min;
                let max = response.data[search_keyword].max;
                changeRangeParams(form_class_name, min, max);
            });
        }).catch(function (error) {
            console.log(error);
        });
    }

    function fromToValidate (string) {
        return string.match(/^\d{1,2},\d{1,3}$/);
    }

    function getFromTo (value) {
        let params = value.split(',');
        let from = null;
        let to = null;
        params.forEach(function (element, index) {
            if (index === 0) {
                from = element;
            } else {
                to = element;
            }
        });
        return {
            from: from,
            to: to,
        }
    }

    const resultHtml = function (name, length, height) {
        let parent_div = document.createElement('div');
        parent_div.style.display = 'flex';
        let child_name_div = document.createElement('div');
        child_name_div.innerText = '名前: ' + name;
        child_name_div.style.marginRight = '5px';
        parent_div.append(child_name_div);
        let child_length_div = document.createElement('div');
        child_length_div.innerText = '長さ: ' + length;
        child_length_div.style.marginRight = '5px';
        parent_div.append(child_length_div);
        let child_height_div = document.createElement('div');
        child_height_div.innerText = '高さ: ' + height;
        parent_div.append(child_height_div);
        return parent_div;
    }

    function changeRangeParams (form_class_name, min, max) {
        let target_elem = $('.' + form_class_name);
        target_elem.jRange('setValue', min + ',' + max);
    }
</script>
</body>
</html>
