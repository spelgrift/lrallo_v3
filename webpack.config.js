var debug = process.env.NODE_ENV !== "production",
path = require('path'),
webpack = require('webpack'),
autoprefixer = require('autoprefixer'),
ExtractTextPlugin = require('extract-text-webpack-plugin');

module.exports = {
  context: __dirname,
  entry: {
    'public': ["./js/public.js"],
    'dashboard': ["./js/dashboard.js"],
    'editPage': ["./js/editPage.js"],
    'editGal': ["./js/editGal.js"],
    'editVid': ["./js/editVid.js"],
    'manageBlog': ["./js/manageBlog.js"],
    'editPost': ["./js/editPost.js"]
  },
  output: {
    path: __dirname + "/public/",
    publicPath: "../", // For CSS url()'s
    filename: "js/[name].min.js"
  },
  plugins: debug ? [ new ExtractTextPlugin("./css/styles.css")] : [
    new webpack.optimize.DedupePlugin(),
    new webpack.optimize.OccurenceOrderPlugin(),
    new webpack.optimize.UglifyJsPlugin({ mangle: false, sourcemap: false }),
  ],
  module: {
    preLoaders: [
      {
        test: /\.js$/,
        include: path.resolve(__dirname, "js/imageman"),
        loader: 'jshint-loader'
      }
    ],
    loaders: [
      {
        test: /\.less$/,
        include: path.resolve(__dirname, "less"),
        loader: ExtractTextPlugin.extract("style-loader", "css-loader!postcss-loader!less-loader")
      },
      { 
        test: /\.(jpg|png|gif|svg)$/,
        exclude: /node_modules/,
        loader: "file-loader?name=images/[name].[ext]"
      }
    ]
  },
  postcss: function() {
    return [autoprefixer];
  }
};