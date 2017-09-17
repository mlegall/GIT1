var size = 0;

var styleCache_Coeur1={}
var style_Coeur1 = function(feature, resolution){
    var value = ""
    var size = 0;
    var style = [ new ol.style.Style({
        
    })];
    if ("" !== null) {
        var labelText = String("");
    } else {
        var labelText = ""
    }
    var key = value + "_" + labelText

    if (!styleCache_Coeur1[key]){
        var text = new ol.style.Text({
              font: '10.725px \'MS Shell Dlg 2\', sans-serif',
              text: labelText,
              textBaseline: "center",
              textAlign: "left",
              offsetX: 5,
              offsetY: 3,
              fill: new ol.style.Fill({
                color: 'rgba(0, 0, 0, 255)'
              }),
            });
        styleCache_Coeur1[key] = new ol.style.Style({"text": text})
    }
    var allStyles = [styleCache_Coeur1[key]];
    allStyles.push.apply(allStyles, style);
    return allStyles;
};