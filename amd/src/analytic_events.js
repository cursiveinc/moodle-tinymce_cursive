// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * TODO describe module analytic_events
 *
 * @module     tiny_cursive/analytic_events
 * @copyright  2024 CTI <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import MyModal from "./analytic_modal";
import { call as getContent } from "core/ajax";
import $ from 'jquery';
import * as Str from 'core/str';
import Chart from 'core/chartjs';
export default class AnalyticEvents {

    createModal(userid, context, questionid = '', authIcon) {
        $('#analytics' + userid + questionid).on('click', function (e) {
            e.preventDefault();

            // Create Moodle modal
            MyModal.create({ templateContext: context }).then(modal => {
                $('#content' + userid + ' .table tbody tr:first-child td:nth-child(2)').html(authIcon);
                modal.show();
            }).catch(error => {
                window.window.console.error("Failed to create modal:", error);
            });
        });
    }

    analytics(userid, templates, context, questionid = '', replayInstances = null, authIcon) {
        $('body').on('click', '#analytic' + userid + questionid, function (e) {
            $('#rep' + userid + questionid).prop('disabled', false);
            e.preventDefault();
            $('#content' + userid).html($('<div>').addClass('d-flex justify-content-center my-5')
                .append($('<div>').addClass('tiny_cursive-loader')));
            if (replayInstances && replayInstances[userid]) {
                replayInstances[userid].stopReplay();
            }
            $('.tiny_cursive-nav-tab').find('.active').removeClass('active');
            $(this).addClass('active'); // Add 'active' class to the clicked element

            templates.render('tiny_cursive/analytics_table', context).then(function (html) {
                $('#content' + userid).html(html);
                $('#content' + userid + ' .table tbody tr:first-child td:nth-child(2)').html(authIcon);

            }).fail(function (error) {
                window.console.error("Failed to render template:", error);
            });
        });
    }

    checkDiff(userid, fileid, questionid = '', replayInstances = null) {
        const nodata = document.createElement('p');
        nodata.classList.add('text-center', 'p-5', 'bg-light', 'rounded', 'm-5', 'text-primary');
        nodata.style.verticalAlign = 'middle';
        nodata.style.textTransform = 'uppercase';
        nodata.style.fontWeight = '500';
        nodata.textContent = "no data received yet";

        $('body').on('click', '#diff' + userid + questionid, function (e) {
            $('#rep' + userid + questionid).prop('disabled', false);
            e.preventDefault();
            $('#content' + userid).html($('<div>').addClass('d-flex justify-content-center my-5')
                .append($('<div>').addClass('tiny_cursive-loader')));
            $('.tiny_cursive-nav-tab').find('.active').removeClass('active');
            $(this).addClass('active'); // Add 'active' class to the clicked element
            if (replayInstances && replayInstances[userid]) {
                replayInstances[userid].stopReplay();
            }
            if (!fileid) {
                $('#content' + userid).html(nodata);
                throw new Error('Missing file id or Difference Content not received yet');
            }
            getContent([{
                methodname: 'cursive_get_writing_differences',
                args: { fileid: fileid },
            }])[0].done(response => {
                let responsedata = JSON.parse(response.data);
                if (responsedata[0]) {
                    let submitted_text = atob(responsedata[0].submitted_text);

                    // Fetch the dynamic strings
                    Str.get_strings([
                        { key: 'original_text', component: 'tiny_cursive' },
                        { key: 'editspastesai', component: 'tiny_cursive' }
                    ]).done(strings => {
                        const originalTextString = strings[0];
                        const editsPastesAIString = strings[1];

                        const $legend = $('<div class="d-flex p-2 border rounded mb-2">');

                        // Create the first legend item
                        const $attributedItem = $('<div>', { class: 'tiny_cursive-legend-item' });
                        const $attributedBox = $('<div>', { class: 'tiny_cursive-box attributed' });
                        const $attributedText = $('<span>').text(originalTextString);
                        $attributedItem.append($attributedBox).append($attributedText);

                        // Create the second legend item
                        const $unattributedItem = $('<div>', { class: 'tiny_cursive-legend-item' });
                        const $unattributedBox = $('<div>', { class: 'tiny_cursive-box tiny_cursive_added' });
                        const $unattributedText = $('<span>').text(editsPastesAIString);
                        $unattributedItem.append($unattributedBox).append($unattributedText);

                        // Append the legend items to the legend container
                        $legend.append($attributedItem).append($unattributedItem);

                        let contents = $('<div>').addClass('tiny_cursive-comparison-content');
                        let textBlock2 = $('<div>').addClass('tiny_cursive-text-block').append(
                            $('<div>').attr('id', 'tiny_cursive-reconstructed_text').html(JSON.parse(submitted_text))
                        );

                        contents.append($legend, textBlock2);
                        $('#content' + userid).html(contents); // Update content
                    }).fail(error => {
                        window.console.error("Failed to load language strings:", error);
                        $('#content' + userid).html(nodata);
                    });
                } else {
                    $('#content' + userid).html(nodata);
                }
            }).fail(error => {
                $('#content' + userid).html(nodata);
                throw new Error('Error loading JSON file: ' + error.message);
            });
        });
    }

    replyWriting(userid, filepath, questionid = '', replayInstances = null) {
        $('body').on('click', '#rep' + userid + questionid, function (e) {
            $(this).prop('disabled', true);
            e.preventDefault();
            $('#content' + userid).html($('<div>').addClass('d-flex justify-content-center my-5')
                .append($('<div>').addClass('tiny_cursive-loader')));
            $('.tiny_cursive-nav-tab').find('.active').removeClass('active');
            $(this).addClass('active'); // Add 'active' class to the clicked element
            if (replayInstances && replayInstances[userid]) {
                replayInstances[userid].stopReplay();
            }
            if (questionid) {
                // eslint-disable-next-line
                video_playback(userid, filepath, questionid);
            } else {
                // eslint-disable-next-line
                video_playback(userid, filepath);
            }
        });
    }

    quality(userid, templates, context, questionid = '', replayInstances = null, cmid) {
        let metricsData = '';
        const nodata = document.createElement('p');
        nodata.classList.add('text-center', 'p-5', 'bg-light', 'rounded', 'm-5', 'text-primary');
        nodata.style.verticalAlign = 'middle';
        nodata.style.textTransform = 'uppercase';
        nodata.style.fontWeight = '500';
        nodata.textContent = "no data received yet";

        $('body').on('click', '#quality' + userid + questionid, function (e) {
            $('#rep' + userid + questionid).prop('disabled', false);
            e.preventDefault();
            $('.tiny_cursive-nav-tab').find('.active').removeClass('active');
            $(this).addClass('active'); // Add 'active' class to the clicked element
          
            getContent([{
                methodname: 'cursive_get_quality_metrics',
                args: { file_id: context.tabledata.file_id ?? userid, cmid: cmid },
            }])[0].done(response => {
                if (response.status) {
                    metricsData = response.data;
                  
                }
            }).fail(error => {

                throw new Error('Error: no data received yet');
            });
            const content = document.getElementById('content' + userid);
            if (content) {
                content.innerHTML = '';
                const loaderDiv = document.createElement('div');
                loaderDiv.className = 'd-flex justify-content-center my-5';
                const loader = document.createElement('div');
                loader.className = 'tiny_cursive-loader';
                loaderDiv.appendChild(loader);
                content.appendChild(loaderDiv);
            }

            if (replayInstances && replayInstances[userid]) {
                replayInstances[userid].stopReplay();
            }

            templates.render('tiny_cursive/quality_chart', context).then(function (html) {
                const content = document.getElementById('content' + userid);

                if (content) content.innerHTML = html;
                if (!metricsData) $('#content' + userid).html(nodata);

                let chartvas = document.querySelector('#chart' + userid);
                const data = {
                    labels: [
                        'Average Word Length',
                        'Edits',
                        'P-burst Count',
                        'P-burst Mean',
                        'Q Count',
                        'Sentence Count',
                        'Total Active Time',
                        'Verbosity',
                        'Word Count',
                        'Word Count per Sentence Mean'
                    ],
                    datasets: [{
                        data: [
                            metricsData.word_len_mean, metricsData.edits, metricsData.p_burst_cnt, metricsData.p_burst_mean,
                            metricsData.q_count, metricsData.sentence_count, metricsData.total_active_time, metricsData.verbosity,
                            metricsData.word_count, metricsData.word_count_per_sentence_mean
                        ],
                        backgroundColor: function (context) {
                            // Apply green or gray depending on value
                            const value = context.raw;
                            if (value > 0 && value < 100) {
                                return '#43BB97';
                            } else if (value < 0) {
                                return '#AAAAAA';
                            } else {
                                return '#00432F'; // Green for positive, gray for negative
                            }

                        },
                    }]
                };

                const drawPercentage = {
                    id: 'drawPercentage',
                    afterDraw: (chart, args, options) => {
                        const { ctx, data } = chart;
                        ctx.save();
                        let value;
                        chart.getDatasetMeta(0).data.forEach((dataPoint, index) => {
                            value = parseInt(data.datasets[0].data[index]);
                            if (!value) {
                                value = 0;
                            }

                            ctx.font = "bold 14px sans-serif";

                            if (value > 25 && value <= 100) {
                                ctx.fillStyle = 'white';
                                ctx.textAlign = 'right';
                                ctx.fillText(value + '%', dataPoint.x - 5, dataPoint.y + 5);
                            } else if (value == 0) {
                            } else if (value > 100) {
                                ctx.textAlign = 'left'; 
                                ctx.fillText(value + '%', - dataPoint.x, dataPoint.y + 5);
                            } else if (value < -25 && value >= -100) {
                                ctx.fillStyle = 'white';
                                ctx.textAlign = 'left';
                                ctx.fillText(value + '%', dataPoint.x + 5, dataPoint.y + 5);
                            } else if (value < -100) {
                                ctx.textAlign = 'right';
                                ctx.fillText(value + '%', dataPoint.x - 5, dataPoint.y + 5);
                            } else if(value > -25 && value < 0){
                                ctx.fillStyle = 'grey';
                                ctx.textAlign = 'right';
                                ctx.fillText(value + '%', dataPoint.x-5, dataPoint.y + 5);
                            } else {
                                ctx.fillStyle = '#43bb97';
                                ctx.textAlign = 'left';
                                ctx.fillText(value + '%', dataPoint.x + 5, dataPoint.y + 5);
                            }




                        });
                    }
                }

                const chartAreaBg = {
                    id: 'chartAreaBg',
                    beforeDraw: (chart) => {
                        const { ctx, scales: { x, y } } = chart;
                        ctx.save();

                        const segmentPixel = y.getPixelForValue(y.ticks[0].value) - y.getPixelForValue(y.ticks[1].value);
                        const doubleSegment = y.ticks[2].value - y.ticks[0].value;
                        let tickArray = [];

                        // Generate tick values
                        for (let i = 0; i <= y.max; i += doubleSegment) {
                            if (i !== y.max) {
                                tickArray.push(i);
                            }
                        }

                        // Draw the background rectangles for each tick
                        tickArray.forEach(tick => {
                            ctx.fillStyle = 'rgba(0, 0, 0, 0.02)';
                            ctx.fillRect(0, y.getPixelForValue(tick) + 52, x.width + x.width + 21, segmentPixel);
                        });

                        // Draw labels and values
                        const dataset = chart.data.datasets[0];
                        const labelOffset = 5; // Offset for positioning

                        // Loop through each tick to draw the text
                        // y.ticks.forEach((tick, index) => {
                        //     // console.log(Object.getPrototypeOf(tick.$context.tick.$context).scale);
                        //     const value = dataset.data[index]; // Get the value for the current label
                        //     const xPosition = x.left; // X position for the text
                        //     const yPosition = y.getPixelForTick(index); // Y position for the tick

                        //     ctx.fillStyle = 'red'; // Set color to match the bar
                        //     ctx.fillText(`${value}%`, xPosition, yPosition+7); // Draw the colored value
                        // });

                        // ctx.restore();
                    }
                };

                new Chart(chartvas, {
                    type: 'bar',
                    data: data,
                    options: {
                        indexAxis: 'y',
                        elements: {
                            bar: {
                                borderRadius: 16,
                                borderWidth: 0,
                            }
                        },
                        responsive: true,
                        scales: {
                            x: {
                                beginAtZero: true,
                                min: -100,
                                max: 100,
                                ticks: {
                                    callback: function (value) {
                                        if (value === -100 || value === 100) {
                                            return value + '%';
                                        } else if (value === 0) {
                                            return 'Average';
                                        }
                                        return '';
                                    },
                                    display: true,
                                    font: function (context) {
                                        if (context && context.tick && context.tick.value === 0) {
                                            return {
                                                weight: 'bold',
                                                size: 14,
                                                color: 'black'
                                            };
                                        }
                                        return {
                                            style: 'semi-bold',
                                            size: 13,
                                            color: 'black'
                                        };
                                    },

                                },
                                grid: {
                                    display: true,
                                    color: function (context) {
                                        return context.tick.value === 0 ? 'black' : '#eaeaea';
                                    },
                                    lineWidth: function (context) {
                                        return context.tick.value === 0 ? 2 : 1; // Thicker line for 0 range
                                    },
                                    tickLength: 0,
                                },
                                position: 'top'

                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    // callback: function (value, index, ticks) {
                                    //     return `${data.labels[index]} ${data.datasets[0].data[index]}%`; // Format the label
                                    // },
                                    display: true,
                                    align: 'center',

                                    crossAlign: 'far',
                                    font: {
                                        size: 18,
                                    },
                                    tickLength: 100,
                                    color: 'black',
                                },
                                grid: {
                                    display: true,
                                    tickLength: 1000,
                                },

                            }
                        },
                        plugins: {
                            legend: {
                                display: false,
                            },
                            title: {
                                display: false,
                            },
                        }
                    },
                    plugins: [chartAreaBg, drawPercentage]
                });

            }).catch(function (error) {
                console.error("Failed to render template:", error);
            });

            document.querySelectorAll('.tiny_cursive-nav-tab .active').forEach(el => el.classList.remove('active'));
            e.target.classList.add('active');

            // templates.render('tiny_cursive/quality_cart', context).then(function (html) {
            //     const content = document.getElementById('content' + userid);
            //     if (content) content.innerHTML = html;



            // }).catch(function (error) {
            //     console.error("Failed to render template:", error);
            // });

        });
    }

    formatedTime(data) {
        if (data.total_time_seconds) {
            let total_time_seconds = data.total_time_seconds;
            let hours = Math.floor(total_time_seconds / 3600).toString().padStart(2, 0);
            let minutes = Math.floor((total_time_seconds % 3600) / 60).toString().padStart(2, 0);
            let seconds = (total_time_seconds % 60).toString().padStart(2, 0);
            return `${hours}h ${minutes}m ${seconds}s`;
        } else {
            return "0h 0m 0s";
        }
    }

    authorshipStatus(firstFile, score, score_setting) {
        var icon = 'fa fa-circle-o';
        var color = 'font-size:32px;color:black';
        var score = parseFloat(score);

        if (firstFile) {
            icon = 'fa fa-solid fa-info-circle';
            color = 'font-size:32px;color:#000000';
        } else if (score >= score_setting) {
            icon = 'fa fa-check-circle';
            color = 'font-size:32px;color:green';
        } else if (score < score_setting) {
            icon = 'fa fa-question-circle';
            color = 'font-size:32px;color:#A9A9A9';
        }

        return $('<i>').addClass(icon).attr('style', color);
    }
}
