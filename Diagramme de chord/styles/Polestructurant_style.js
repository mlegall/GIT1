var size = 0;

var styleCache_Polestructurant={}
var style_Polestructurant = function(feature, resolution){
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

    if (!styleCache_Polestructurant[key]){
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
        styleCache_Polestructurant[key] = new ol.style.Style({"text": text})
    }
    var allStyles = [styleCache_Polestructurant[key]];
    allStyles.push.apply(allStyles, style);
    return allStyles;
};