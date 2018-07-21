var editHotSpot = [];
var deletedHotSpot = [];
var krpano, polygon = null;
var totalHS = 0;
var drawing = false;
var firstAdd = false;
var scenes = [];
var running = false;
(function($){
    $.fn.myPano = function () {
        var swf = $(this).attr('data-swf');
        var xml = $(this).attr('data-xml');
        embedpano({
            swf: swf,
            xml: xml,
            target: "pano",
            html5: "auto",
            mobilescale: 1.0,
            passQueryParameters: false,
            onready: function (krpano_interface) {
                krpano = krpano_interface;
            }
        });
    };

})(jQuery);

function openModelVideos(videoURL, HSName) {
    var dialog = $('#m-dialog');
    dialog.html('<div class="modal-content">' +
        '        <div class="modal-header">' +
        '            <h4 class="modal-title"></h4>' +
        '        </div>' +
        '        <div class="modal-body"></div>' +
        '        <div class="modal-footer">' +
        '            <button type="button" class="btn btn-default close-dialog" data-dismiss="modal">Close</button>' +
        '            <button type="button" class="btn btn-primary submit" disabled>Submit</button>' +
        '        </div>' +
        '</div>');
    dialog.find('.modal-title').text('Video');
    dialog.find(".close-dialog").click(function(){
        dialog.html("");
        return false;
    });

    dialog.find(".submit").click(function(){
        var src = $(this).closest('.modal-content').find('li.active a').attr('data-src');
        var poster = $(this).closest('.modal-content').find('li.active a').attr('data-poster');
        if (videoURL && HSName) {
            krpano.set("hotspot[" + HSName + "].videourl",src);
            krpano.call("hotspot["+HSName+"].playvideo("+src+");");
        } else {
            //general id
            HSName = generalID('video');
            var hlookat = Number( krpano.get("view.hlookat") );
            var vlookat = Number( krpano.get("view.vlookat") );
            var HSAttributes = {
                name: HSName,
                url: AWS_URL + '/krpano/plugins/videoplayer.js',
                videourl: src,
                posterurl: poster,
                volume: 1,
                ath: hlookat,
                atv: vlookat,
                ox: 0,
                oy: 0,
                rx: 15,
                ry: 20,
                rz: -4,
                scale: 1,
                alpha: 1,
                width: 200,
                height: 200,
                rotate: 0,
                loop: true,
                distorted: true,
                onclick: 'focusVideoHS()',
                style: 'dragableVideo',
                onvideoready: 'js(hideShadow())',
                pausedonstart: "false",
                scene: krpano.get("scene[get(xml.scene)].id"),
                mtype: 'video'
            };
            krpano.call("addhotspot(" + HSName + ");");
            for (var key in HSAttributes) {
                if (HSAttributes.hasOwnProperty(key) && key !== "name") {
                    krpano.call("set(hotspot[" + HSName + "]."+key+","+HSAttributes[key]+");");
                }
                if (HSAttributes.hasOwnProperty(key) && key === "style") {
                    krpano.call("hotspot[" + HSName + "].loadstyle("+HSAttributes[key]+");");
                }
            }
        }
        dialog.html('');
        showShadow('Loading...');
        addOrUpdateHS(HSName);
        return false;
    });

    $.ajax({
        url: '/admin/video/list',
        data: {panorama_id: $('#panoramaId').val()},
        dataType: 'json',
        success: function(response) {
            dialog.find('.modal-body').html('<ul></ul>');
            var html = '';
            response.results.forEach(function(item){
                var active = '';
                var data = $.parseJSON(item.data);
                var src = data.desktop.mp4 + '|' + data.desktop.webm;
                if (src === videoURL) {
                    active += 'class="active"';
                }
                html += '<li '+active+'><a href="#" data-src="'+data.desktop.mp4+'|'+data.desktop.webm+'" data-poster="'+item.preview+'"><img src="'+item.preview+'"/><p>'+item.name+'</p></a></li>'
            });
            dialog.find('.modal-body ul').html(html);
            dialog.find("ul a").on("click", function(){
                $(this).closest('.modal-content').find('li').removeClass('active');
                $(this).parent().addClass('active');
                $(this).closest('.modal-content').find('.submit').prop('disabled', false);
                return false;
            });
        }
    });
}
function showShadow(text) {
    if (text) {
        $('.dialog-overlay').html('<div class="loading">'+text+'</div>');
    }
    $('.dialog-overlay').fadeIn();
}

function hideShadow()
{
    $('.dialog-overlay').html('');
    $('.dialog-overlay').fadeOut();
}
function deleteDialog(title, calback)
{
    var dialog = $('#m-dialog');
    showShadow();
    dialog.html('<div class="modal-content">' +
        '        <div class="modal-body"><div style="font-size: 19px;font-weight: normal;padding-top: 20px;">'+title+'</div></div>' +
        '        <div class="modal-footer" style="border-top:none">' +
        '            <button type="button" class="btn btn-default close-dialog" data-dismiss="modal">Cancel</button>' +
        '            <button type="button" class="btn btn-danger submit">Delete</button>' +
        '        </div>' +
        '</div>');
    dialog.find('.modal-title').text('Video');
    dialog.find(".close-dialog").click(function(){
        hideShadow();
        dialog.html("");
        return false;
    });

    dialog.find(".submit").click(function(){
        calback();
        hideShadow();
        dialog.html("");
    });
}


function openModelIcons(iconURL, HSName) {
    var dialog = $('#m-dialog');
    dialog.html('<div class="modal-content">' +
        '        <div class="modal-header">' +
        '            <h4 class="modal-title"></h4>' +
        '        </div>' +
        '        <div class="modal-body"></div>' +
        '        <div class="modal-footer">' +
        '            <button type="button" class="btn btn-default close-dialog" data-dismiss="modal">Close</button>' +
        '            <button type="button" class="btn btn-primary submit" disabled>Submit</button>' +
        '        </div>' +
        '</div>');
    dialog.find('.modal-title').text('Choose Icon');
    dialog.find(".close-dialog").click(function(){
        dialog.html("");
        return false;
    });

    dialog.find(".submit").click(function(){
        var src = $(this).closest('.modal-content').find('li.active a').attr('data-url');
        console.log(iconURL);
        console.log(HSName);
        if (iconURL && HSName) {
            krpano.set("hotspot[" + HSName + "].url",src);
        } else {
            //general id
            HSName = generalID('hs');
            var hlookat = Number( krpano.get("view.hlookat") );
            var vlookat = Number( krpano.get("view.vlookat") );
            var HSAttributes = {
                name: HSName,
                url: src,
                ath: hlookat,
                atv: vlookat,
                width: 30,
                height:30,
                keep: false,
                style: 'dragableHotspot',
                linktotitle: 'none',
                onclick: 'focusHS()',
                onover: 'overParentTag()',
                onout: 'outParentTag()',
                scene: krpano.get("scene[get(xml.scene)].id"),
                mtype: 'icon'
            };
            krpano.call("addhotspot(" + HSName + ");");
            for (var key in HSAttributes) {
                if (HSAttributes.hasOwnProperty(key) && key !== "name") {
                    krpano.call("set(hotspot[" + HSName + "]."+key+","+HSAttributes[key]+");");
                }
                if (HSAttributes.hasOwnProperty(key) && key === "style") {
                    krpano.call("hotspot[" + HSName + "].loadstyle("+HSAttributes[key]+");");
                }
            }
        }

        dialog.html('');
        addOrUpdateHS(HSName);
        return false;
    });

    $.ajax({
        url: '/admin/icon/list',
        data: {panorama_id: $('#panoramaId').val()},
        dataType: 'json',
        cache:true,
        success: function(response) {
            dialog.find('.modal-body').html('<ul class="icons"></ul>');
            var html = '';
            response.results.forEach(function(item){
                var active = '';
                if(item.url === iconURL) {
                    active += 'class="active"';
                }
                html += '<li '+active+'><a href="#" data-url="'+item.url+'"><img src="'+item.url+'"/><p>'+item.name+'</p></a></li>'
            });
            dialog.find('.modal-body ul').html(html);
            dialog.find("ul a").on("click", function(){
                $(this).closest('.modal-content').find('li').removeClass('active');
                $(this).parent().addClass('active');
                $(this).closest('.modal-content').find('.submit').prop('disabled', false);
                return false;
            });
        }
    });
}

function openModelScenes(sceneName, HSName) {
    var dialog = $('#m-dialog');
    dialog.html('<div class="modal-content">' +
        '        <div class="modal-header">' +
        '            <h4 class="modal-title"></h4>' +
        '        </div>' +
        '        <div class="modal-body"></div>' +
        '        <div class="modal-footer">' +
        '            <button type="button" class="btn btn-default close-dialog" data-dismiss="modal">Close</button>' +
        '            <button type="button" class="btn btn-primary submit" disabled>Submit</button>' +
        '        </div>' +
        '</div>');
    dialog.find('.modal-title').text('Choose Icon');
    dialog.find(".close-dialog").click(function(){
        dialog.html("");
        return false;
    });

    dialog.find(".submit").click(function(){
        var sceneItem = $(this).closest('.modal-content').find('li.active a');
        if (HSName) {
            var name = sceneItem.attr('data-name');
            var title = sceneItem.attr('data-title');
            krpano.set("hotspot[" + HSName + "].linktoname",name);
            krpano.set("hotspot[" + HSName + "].linktotitle",title);
            krpano.set("hotspot[" + HSName + "].onclick","focusHS()");
        }

        dialog.html('');
        addOrUpdateHS(HSName);
        generalProperties(HSName);
        return false;
    });

    $.ajax({
        url: '/admin/scene/list',
        data: {panorama_id: $('#panoramaId').val()},
        dataType: 'json',
        success: function(response) {
            dialog.find('.modal-body').html('<ul class="icons"></ul>');
            var html = '';
            response.results.scenes.forEach(function(item){
                var active = '';
                var name = 'scene_'+item.id;
                if (sceneName === name) {
                    active = 'class="active"';
                }
                html += '<li '+active+'><a href="#" data-name="'+name+'" data-title="'+item.name+'" data-id="'+item.id+'">' +
                    '        <img src="'+item.thumb+'" />' +
                    '        <p>'+item.name+'</p>' +
                    '   </a></li>';
            });
            response.results.videos.forEach(function(item){
                var active = '';
                var name = 'video'+item.id;
                if (sceneName === name) {
                    active = 'class="active"';
                }
                html += '<li '+active+'><span class="label-360">360 video</span>' +
                    '<a href="#" data-name="'+name+'" data-title="'+item.name+'" data-id="'+item.id+'">' +
                    '        <img src="'+item.preview+'" />' +
                    '        <p>'+item.name+'</p>' +
                    '   </a></li>';
            });

            dialog.find('.modal-body ul').html(html);
            dialog.find("ul a").on("click", function(){
                $(this).closest('.modal-content').find('li').removeClass('active');
                $(this).parent().addClass('active');
                $(this).closest('.modal-content').find('.submit').prop('disabled', false);
                return false;
            });
        }
    });
}
function focusVideoHS(name)
{
    if (running === true) {
        return false;
    }
    running = true;
    setTimeout(function(){
        generalProperties(name);
        running = false;
    }, 80);
}
function focusHS(name)
{
    if (running === true) {
        return false;
    }
    running = true;
    setTimeout(function(){
        generalProperties(name);
        running = false;
    }, 80);
}

function generalProperties(name)
{
    var data = krpano.get("hotspot['"+name+"']");
    var setting = $("#setting-box .general");
    setting.html('<div class="item option" style="margin-bottom: 10px">' +
        '               <div class="title"><span></span><a href="#" class="m-close">x</a></div>' +
        '               <div class="content"></div>' +
        '         </div>');

    var content = setting.find('.option .content');
    setting.find('.option .title span').text(data.name);
    content.append('<input type="hidden" id="hsName" value="'+data.name+'" />');

    console.log(data.linktotitle);
    console.log(data);
    if (data.mtype === 'icon' && data.dataavatar) {
        content.append('<div style="text-align: center"><img style="max-width: 100%" src="'+data.dataavatar+'" /></div>');
    }
    if (data.mtype === 'icon' && data.dataname) {
        content.append('<div class="line"><label>Name</label><a href="'+data.dataurl+'" target="_blank">'+data.dataname+'</a></div>');
    }

    if (data.linktotitle) {
        content.append('<div class="line"><label>Link to</label><a href="#" onclick="openModelScenes(\'' + data.linktoname +'\',\''+ name+'\');return false">'+data.linktotitle+'</></div>');
    }

    if (data.mtype === 'video' && !isNaN(data.width) && data.width !== null) {
        content.append('<div class="line"><label>Width</label><input type="number" class="input-drag" name="width" value="'+data.width+'" /></div>');
    }
    if (data.mtype === 'video' && !isNaN(data.height) && data.height !== null) {
        content.append('<div class="line"><label>Height</label><input type="number" class="input-drag" name="height" value="'+data.height+'" /></div>');
    }
    if (data.mtype !== 'poly' && !isNaN(data.rotate) && data.rotate !== null) {
        content.append('<div class="line"><label>Rotate</label><input type="number" class="input-drag" name="rotate" value="'+data.rotate+'" /></div>');
    }
    if (data.mtype !== 'poly' && !isNaN(data.scale) && data.scale !== null) {
        content.append('<div class="line"><label>Scale</label><input type="number" class="input-drag" name="scale" value="'+data.scale+'" /></div>');
    }
    if (data.mtype !== 'poly' && !isNaN(data.ath) && data.ath !== null) {
        content.append('<div class="line"><label>Ath</label><input type="number" class="input-drag" name="ath" value="'+round(data.ath,4)+'" /></div>');
    }
    if (data.mtype !== 'poly' && !isNaN(data.atv) && data.atv !== null) {
        content.append('<div class="line"><label>Atv</label><input type="number" class="input-drag" name="atv" value="'+round(data.atv,4)+'" /></div>');
    }
    if (data.mtype === 'video' && !isNaN(data.rx) && data.rx !== null) {
        content.append('<div class="line"><label>Rx</label><input type="number" class="input-drag" name="rx" value="'+round(data.rx,4)+'" /></div>');
    }

    if (data.mtype === 'video' && !isNaN(data.ry) && data.ry !== null) {
        content.append('<div class="line"><label>Ry</label><input type="number" class="input-drag" name="ry" value="'+round(data.ry,4)+'" /></div>');
    }

    if (data.mtype === 'video' && !isNaN(data.rz) && data.rz !== null) {
        content.append('<div class="line"><label>Rz</label><input type="number" class="input-drag" name="rz" value="'+round(data.rz,4)+'" /></div>');
    }

    if (data.mtype === 'video'){
        content.append('<div class="line text-center bt-play" style="background-color: #fff;color: #000;">Play</div>');
        content.append('<div class="line text-center bt-pause" style="background-color: #fff;color: #000;">Pause</div>');
    }

    if (data.mtype === 'icon') {
        content.append('<div class="line text-center bt-change"  onclick="openModelIcons(\'' + data.url +'\',\''+ name+'\')" style="background-color: #fff;color: #000;">Change Icon</div>');
    }
    if (data.mtype === 'video') {
        content.append('<div class="line text-center" onclick="openModelVideos(\'' + data.videourl +'\',\''+ name+'\')" style="background-color: #fff;color: #000;">Change Video</div>');
    }

    if (data.point && data.point !== null) {
        for(var i = 0; i < data.point.count; i++) {
            var point = data.point.getItem(i);
            var index = i+1;
            content.append('<div class="line" style="height: 50px"><label style="vertical-align: text-bottom">Point '+index+'</label>' +
                '<span  data-key="point" data-name="'+point.name+'" data-index="'+point.index+'" style="display: inline-block;width: 60%;">' +
                '<input type="number" class="input-drag full" data-attribute="ath" name="point['+point.index+'].ath" value="'+round(point.ath,4)+'" />' +
                '<input type="number" class="input-drag full" data-attribute="atv" name="point['+point.index+'].atv" value="'+round(point.atv,4)+'" />' +
                '</div>');
        }
    }
    setting.find('.option .content').append('<button class="text-center btn btn-danger bt-delete" data-name="'+name+'">Remove</button>');

    $('.input-drag').bind("mousewheel change keyup", function(){
        var name = $('#hsName').val();
        krpano.call("set(hotspot[" + name + "]."+$(this).attr('name')+","+$(this).val()+");");
        krpano.call("updatescreen();");
        if ($(this).parent().attr('data-key') === 'point') {
            var newPoints = [];
            $(this).closest('.option').find('span[data-key="point"]').each(function(i, obj) {
                newPoints.push({
                    name:$(obj).attr('data-name'),
                    ath:$(obj).find('input[data-attribute="ath"]').val(),
                    atv:$(obj).find('input[data-attribute="atv"]').val(),
                    index:$(obj).attr('data-index')
                });
            });
        }
        addOrUpdateHS(name);
    });
    $('.bt-play').click(function(){
        krpano.call("hotspot["+name+"].play();");
    });
    $('.m-close').click(function(){
        $('#setting-box .general').html('');
        return false;
    });
    $('.bt-pause').click(function(){
        krpano.call("hotspot["+name+"].pause();");
    });
    $('.bt-delete').click(function(){
        var name = $(this).attr('data-name');
        deleteDialog("Are you sure you want to delete "+name+"?", function(){
            var hs = krpano.get("hotspot['"+name+"']");
            deletedHotSpot.push({
                name: hs.name,
                scene: hs.scene
            });
            krpano.call("removehotspot("+name+")");
            for (var i = 0; i < editHotSpot.length; i++) {
                if (editHotSpot[i].name === name) {
                    editHotSpot.splice(i, 1);
                }
            }
            $("#setting-box").html('<div class="general"></div>');
            $("#list-box").html('');
        });
    });

    addOrUpdateHS(name);
}

function generalID(prefix)
{
    if (!firstAdd) {
        totalHS = krpano.get('hotspot.count');
        firstAdd = true;
    }
    totalHS++;
    return prefix + "-" + totalHS;
}

function generalNewHotSpot()
{
    var sceneID = krpano.get("scene[get(xml.scene)].id");
    $("#setting-box").html('<div class="general"></div>');
    $("#list-box").html('');

    editHotSpot.forEach(function(item){
        if (item["scene"] == sceneID) {
            var name = null;
            for (var key in item) {
                if (key === "name") {
                    name = item[key];
                    krpano.call("addhotspot(" + name + ");");
                }

                if (name && item.hasOwnProperty(key) && key !== "name") {
                    krpano.call("set(hotspot[" + name + "]."+key+","+item[key]+");");
                }
                if (key === "points" && item['style']) {
                    krpano.call("hotspot[" + name + "].loadstyle("+item['style']+");");
                    var points = item[key];
                    for (var i in points) {
                        krpano.call("set(hotspot[" + name + "].point["+points[i].index+"].ath,"+points[i].ath+");");
                        krpano.call("set(hotspot[" + name + "].point["+points[i].index+"].atv,"+points[i].atv+");");
                    }
                }
            }
        }
    });

    deletedHotSpot.forEach(function(item){
        if (item["scene"] == sceneID) {
            var name = null;
            for (var key in item) {
                if (key === "name") {
                    name = item[key];
                    krpano.call("removehotspot("+name+")");
                }
            }
        }
    });
}


function round(number, precision) {
    var shift = function (number, precision, reverseShift) {
        if (reverseShift) {
            precision = -precision;
        }
        var numArray = ("" + number).split("e");
        return +(numArray[0] + "e" + (numArray[1] ? (+numArray[1] + precision) : precision));
    };
    return shift(Math.round(shift(number, precision, false)), precision, true);
}

function startDraw()
{
    drawing = true;
    notify('<b>Click</b> or <b>touch</b> on screen to draw. Finish by <b>Space bar</b> or click <b>Stop draw</b> button');
    $('#polyHSButton').text('Stop Draw');
    $('#polyHSButton').attr('data-status', 'stop');
    $('#polyHSButton').addClass('active');
    polygon = generalID("polyhs");

    krpano.set("polygon", polygon);
    krpano.set("drawing", true);
    krpano.set("polygonname", polygon);
    krpano.call("addhotspot("+polygon+");");
    krpano.call("hotspot["+polygon+"].loadstyle(newpoly);");
    krpano.set("hotspot["+polygon+"].enabled", false);
    krpano.set("hotspot["+polygon+"].ondown", "draghotspot()");
    krpano.set("hotspot["+polygon+"].linktotitle", 'none');
    krpano.set("pid", 0);
}

function stopDraw() {
    if (drawing === true) {
        $('#polyHSButton').text('Add Polygon Hotspot');
        $('#polyHSButton').attr('data-status', 'start');
        $('#polyHSButton').removeClass('active');
        krpano.set("drawing", false);
        krpano.set("hotspot["+polygon+"].mtype", "poly");
        krpano.set("hotspot["+polygon+"].onclick", "focusHS()");
        krpano.set("hotspot["+polygon+"].enabled", true);
        krpano.set("hotspot["+polygon+"].scene", krpano.get("scene[get(xml.scene)].id"));
        krpano.call("updatescreen();");
        drawing = false;
        addOrUpdateHS(polygon);
    }
}

function updateXML()
{
    var data = {
        setting: scenes,
        editHotSpots: editHotSpot,
        deletedHotSpots: deletedHotSpot
    };
    console.log(data);
    $.ajax({
        url:'/admin/panorama/update-xml',
        type: 'POST',
        data: data,
        dataType: 'json',
        beforeSend: function() {
            showShadow('Processing...')
        },
        success: function(res) {
            if (res.success) {
                hideShadow();
                notify('Updated successfully!', {time: 2000, type: 'success'});
            }
        }
    })
}

function setLimitZoom()
{
    var sceneID = krpano.get("scene[get(xml.scene)].id");
    addOrUpdateScenes(scenes, sceneID, {
        fovmin: krpano.get('view.fov')
    });
    notify('The limit zoom has been updated. Click <b>Save</b> to complete!', {time: 2000, type: 'success'});
}

function setDefaultView()
{
    var sceneID = krpano.get("scene[get(xml.scene)].id");
    addOrUpdateScenes(scenes, sceneID, {
        fov: krpano.get('view.fov'),
        hlookat: krpano.get('view.hlookat'),
        vlookat: krpano.get('view.vlookat')
    });
    notify('Default view has been updated. Click <b>Save</b> to complete!', {time: 2000, type: 'success'});
}

function addOrUpdateScenes(scenes, id, data)
{
    for (var i = 0; i < scenes.length; i++) {
        if (scenes[i].scene === id) {
            for (var key in data) {
                scenes[i][key] = data[key];
            }
            return scenes;
        }
    }

    var newData = {
        scene: id
    };

    for (var attr in data) {
        newData[attr] = data[attr];
    }

    scenes.push(newData);
    return scenes;
}

function notify(text, options = {})
{
    var delay = 4000;
    var css = {
        color: '#ffffff',
        background: 'rgba(0, 0, 0, 0.8)'
    };

    if (options.time) {
        delay = options.time;
    }
    if (options.type && options.type === 'success') {
        css.background = 'rgba(25, 108, 52, 0.9)';
    }
    var elm = $('#notify');
    elm.html(text);
    elm.css(css);
    elm.fadeIn();
    setTimeout(function(){
        elm.fadeOut();
    }, delay)
}

function addOrUpdateHS(name)
{
    if (name === 'skin_webvr_prev_scene') return;
    for (var i = 0; i < editHotSpot.length; i++) {
        if (editHotSpot[i].name === name) {
            editHotSpot.splice(i, 1);
        }
    }
    var hs = krpano.get("hotspot['"+name+"']");
    var data = {
        name: hs.name,
        atv: hs.atv,
        ath: hs.ath,
        scale: hs.scale,
        rotate: hs.rotate,
        rx: hs.rx,
        ry: hs.ry,
        rz: hs.rz,
        url: hs.url,
        alpha: hs.alpha,
        width: hs.width,
        height: hs.height,
        loop: hs.loop,
        distorted: hs.distorted,
        scene: hs.scene,
        onclick: hs.onclick,
        mtype: hs.mtype
    };
    if(hs.point) {
        var points = [];
        for(var i = 0; i < hs.point.count; i++) {
            points.push({
                name:hs.point.getItem(i).name,
                ath:hs.point.getItem(i).ath,
                atv:hs.point.getItem(i).atv,
                index:hs.point.getItem(i).index
            });
        }
        data.points = points;
    }

    if(hs.onvideoready) {
        data.onvideoready = hs.onvideoready;
    }

    if(hs.pausedonstart) {
        data.pausedonstart = hs.pausedonstart;
    }

    if(hs.videourl) {
        data.videourl = hs.videourl;
    }

    if(hs.posterurl) {
        data.posterurl = hs.posterurl;
    }

    if(hs.volume) {
        data.volume = hs.volume;
    }

    if(hs.style) {
        data.style = hs.style;
    }

    if(hs.onover) {
        data.onover = hs.onover;
    }

    if(hs.onout) {
        data.onout = hs.onout;
    }

    if(hs.ondown) {
        data.ondown = hs.ondown;
    }

    if(hs.dataurl) {
        data.dataUrl = hs.dataurl;
    }

    if(hs.dataavatar) {
        data.dataAvatar = hs.dataavatar;
    }

    if(hs.dataname) {
        data.dataName = hs.dataname;
    }

    if(hs.dataicon) {
        data.dataIcon = hs.dataicon;
    }

    if(hs.linktotitle) {
        data.linktotitle = hs.linktotitle;
        data.linktoname = hs.linktoname;
    }
    editHotSpot.push(data);
}

$('#polyHSButton').click(function(){
    if ($(this).attr('data-status') === 'start') {
        startDraw();
    } else {
        stopDraw();
    }
    return false;
});




