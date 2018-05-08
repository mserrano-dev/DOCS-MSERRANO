const path = require("path");
const webpack = require("webpack");
const ExtractTextPlugin = require("extract-text-webpack-plugin");

module.exports = (env, argv) => {
    const app_name = "docs"; //APP_NAME
    const production = (argv.mode === "production");

    const extract_src_styles = new ExtractTextPlugin("./public/" + app_name + ".mserrano.css");
    const extract_ut_styles = new ExtractTextPlugin("./tmp/sass-output-report/sass-true.output.css");

    let config = {
        entry: {
            [app_name]: "./frontend/build/entry.js",
            vendor: "./frontend/build/vendor.js",
            styles: "./frontend/build/styles.js",
            template: './frontend/build/template.js',
            sassTrue: './frontend/tests/sass-true.output.js',
        },
        output: {
            filename: (arg) => {
                const blacklist = ["template", "styles", "sassTrue"];
                const is_scratch = (blacklist.indexOf(arg.chunk.name) !== -1);

                return (is_scratch === true ? "./tmp/trash/[name].scratch" : "./public/[name].mserrano.js");
            },
            path: path.resolve(__dirname),
        },
        module: {
            rules: [
                {test: /\.(js)$/, loader: "ng-annotate-loader"},
                {test: /\.(html)$/, loader: "angular-templatecache-loader?module=" + app_name},
                {test: /^((?!(\.spec)).)*\.scss$/, use: extract_src_styles.extract([
                        {
                            loader: "css-loader"
                        },
                        {
                            loader: "sass-loader",
                            options: {includePaths: [path.resolve(__dirname, "./library/styles")]}
                        }
                    ])},
                {test: /\.spec\.scss$/, use: extract_ut_styles.extract([
                        {
                            loader: "css-loader"
                        },
                        {
                            loader: "sass-loader",
                            options: {includePaths: [
                                    path.resolve(__dirname, "./library/styles"),
                                    path.resolve(__dirname, "./frontend/tests/styles"),
                                    path.resolve(__dirname, "./node_modules/sass-true/sass"),
                                ]}
                        }
                    ])},
            ],
        },
        plugins: [
            extract_src_styles,
            extract_ut_styles,
        ],
    };

    if (production === false) {
        config.devtool = "inline-cheap-source-map";
    }
    return config;
};