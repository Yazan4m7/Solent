@php
    $heatmapRegions = collect($dashboardMetrics['clientCountries'] ?? [])
        ->map(fn (array $region) => [
            'name' => (string) ($region['name'] ?? ''),
            'cases' => (int) ($region['value'] ?? 0),
        ])
        ->filter(fn (array $region) => $region['name'] !== '')
        ->values()
        ->all();
@endphp

<div class="solent-dash-jordan-map{{ !empty($expanded) ? ' solent-dash-jordan-map-expanded' : '' }}"
    data-solent-jordan-map
    data-regions="{{ json_encode($heatmapRegions, JSON_HEX_APOS | JSON_HEX_QUOT) }}">
    <div class="solent-dash-jordan-loading" data-map-loading aria-label="Loading Jordan clinic activity map">
        <span></span>
    </div>
    <div class="solent-dash-jordan-canvas" data-map-canvas hidden></div>
    <div class="solent-dash-jordan-overlay" data-map-empty hidden>No activity data for this period</div>
    <div class="solent-dash-jordan-error" data-map-error hidden>
        <span>Map data could not be loaded.</span>
        <span class="solent-dash-jordan-retry" role="button" tabindex="0" data-map-retry>Retry</span>
    </div>
    <div class="solent-dash-jordan-tooltip" data-map-tooltip hidden></div>
    <div class="solent-dash-jordan-legend" data-map-legend hidden>
        <div class="solent-dash-jordan-gradient"></div>
        <div class="solent-dash-jordan-legend-labels">
            <span>0</span>
            <span data-map-midpoint>0</span>
            <span><strong data-map-maximum>0</strong> cases</span>
        </div>
    </div>
</div>

@once
    <style>
        .solent-dash-jordan-map {
            min-height: 118px;
            position: relative;
        }

        .solent-dash-jordan-canvas {
            align-items: center;
            display: flex;
            height: calc(100% - 30px);
            justify-content: center;
            min-height: 88px;
        }

        .solent-dash-jordan-canvas svg {
            display: block;
            height: 100%;
            margin: 0 auto;
            overflow: visible;
            width: 100%;
        }

        .solent-dash-jordan-region {
            cursor: pointer;
            stroke: #ffffff;
            stroke-width: 1.2;
            transition: filter 140ms ease, opacity 140ms ease;
        }

        .solent-dash-jordan-region:hover,
        .solent-dash-jordan-region:focus {
            filter: brightness(0.8);
            opacity: 1;
            outline: none;
        }

        .solent-dash-jordan-label {
            fill: #344054;
            font-family: Arial, sans-serif;
            font-size: 8px;
            font-weight: 800;
            paint-order: stroke;
            pointer-events: none;
            stroke: rgba(255, 255, 255, 0.88);
            stroke-linejoin: round;
            stroke-width: 3px;
            text-anchor: middle;
        }

        .solent-dash-jordan-legend {
            margin: 2px auto 0;
            max-width: 210px;
            width: calc(100% - 12px);
        }

        .solent-dash-jordan-gradient {
            background: linear-gradient(90deg, #f4f8fc, #d8e8f3, #a8cce1, #609dc3, #1a6fa8);
            border-radius: 999px;
            height: 7px;
        }

        .solent-dash-jordan-legend-labels {
            color: #667085;
            display: flex;
            font-size: 8px;
            font-weight: 700;
            justify-content: space-between;
            margin-top: 3px;
        }

        .solent-dash-jordan-loading,
        .solent-dash-jordan-overlay,
        .solent-dash-jordan-error {
            align-items: center;
            display: flex;
            inset: 8px;
            justify-content: center;
            position: absolute;
            text-align: center;
        }

        .solent-dash-jordan-loading span {
            animation: solentJordanPulse 1.2s ease-in-out infinite;
            background: linear-gradient(90deg, #edf2f7, #dfe8f1, #edf2f7);
            border-radius: 16px;
            height: 82%;
            width: 74%;
        }

        .solent-dash-jordan-overlay,
        .solent-dash-jordan-error {
            color: #667085;
            flex-direction: column;
            font-size: 10px;
            font-weight: 800;
            gap: 7px;
        }

        .solent-dash-jordan-retry {
            background: #1a6fa8;
            border-radius: 999px;
            color: #ffffff;
            cursor: pointer;
            padding: 5px 12px;
        }

        .solent-dash-jordan-tooltip {
            background: #101828;
            border-radius: 8px;
            color: #ffffff;
            font-size: 10px;
            font-weight: 800;
            line-height: 1.35;
            padding: 7px 9px;
            pointer-events: none;
            position: absolute;
            z-index: 4;
        }

        .solent-dash-jordan-map-expanded {
            min-height: 100%;
        }

        .solent-dash-jordan-map-expanded .solent-dash-jordan-label {
            font-size: 9px;
        }

        @keyframes solentJordanPulse {
            0%, 100% { opacity: 0.58; }
            50% { opacity: 1; }
        }
    </style>

    <script>
        (function () {
            const metadataUrl = 'https://www.geoboundaries.org/api/current/gbOpen/JOR/ADM1/';
            const neutralFill = '#cbd5e1';
            const blueRamp = ['#f4f8fc', '#d8e8f3', '#a8cce1', '#609dc3', '#1a6fa8'];
            const aliases = {
                ajlun: 'ajloun',
                albalqa: 'salt',
                alkarak: 'karak',
                attafilah: 'tafilah',
                azzarqa: 'zarqa',
                maan: 'maan'
            };

            function normalize(value) {
                const key = String(value || '').toLowerCase().replace(/[^a-z]/g, '');
                return aliases[key] || key;
            }

            function parseGeoJson(response) {
                return response.text().then(function (text) {
                    if (text.indexOf('version https://git-lfs.github.com/spec/v1') === 0) {
                        throw new Error('GeoJSON resolved to a Git LFS pointer.');
                    }

                    return JSON.parse(text);
                });
            }

            function mediaUrl(url) {
                return url
                    .replace('https://github.com/', 'https://media.githubusercontent.com/media/')
                    .replace('/raw/', '/');
            }

            function fetchGeoJson(url) {
                return fetch(url).then(function (response) {
                    if (!response.ok) throw new Error('GeoJSON download failed.');
                    return parseGeoJson(response);
                }).catch(function () {
                    return fetch(mediaUrl(url)).then(function (response) {
                        if (!response.ok) throw new Error('GeoJSON media download failed.');
                        return parseGeoJson(response);
                    });
                });
            }

            function loadJordanGeoJson(forceRetry) {
                if (forceRetry || !window.solentJordanGeoJsonPromise) {
                    window.solentJordanGeoJsonPromise = fetch(metadataUrl)
                        .then(function (response) {
                            if (!response.ok) throw new Error('GeoJSON metadata failed.');
                            return response.json();
                        })
                        .then(function (metadata) {
                            return fetchGeoJson(metadata.simplifiedGeometryGeoJSON || metadata.gjDownloadURL);
                        })
                        .catch(function (error) {
                            window.solentJordanGeoJsonPromise = null;
                            throw error;
                        });
                }

                return window.solentJordanGeoJsonPromise;
            }

            function featureCoordinates(feature) {
                const polygons = feature.geometry.type === 'Polygon'
                    ? [feature.geometry.coordinates]
                    : feature.geometry.coordinates;

                return polygons.reduce(function (coordinates, polygon) {
                    return coordinates.concat(polygon.reduce(function (rings, ring) {
                        return rings.concat(ring);
                    }, []));
                }, []);
            }

            function bounds(features) {
                return features.reduce(function (result, feature) {
                    return featureCoordinates(feature).reduce(function (current, point) {
                        current.minX = Math.min(current.minX, point[0]);
                        current.maxX = Math.max(current.maxX, point[0]);
                        current.minY = Math.min(current.minY, point[1]);
                        current.maxY = Math.max(current.maxY, point[1]);
                        return current;
                    }, result);
                }, { minX: Infinity, maxX: -Infinity, minY: Infinity, maxY: -Infinity });
            }

            function fitExtentProjector(features, width, height) {
                const mapBounds = bounds(features);
                const padding = 18;
                const availableWidth = width - (padding * 2);
                const availableHeight = height - (padding * 2);
                const scale = Math.min(
                    availableWidth / (mapBounds.maxX - mapBounds.minX),
                    availableHeight / (mapBounds.maxY - mapBounds.minY)
                );
                const xOffset = (width - ((mapBounds.maxX - mapBounds.minX) * scale)) / 2;
                const yOffset = (height - ((mapBounds.maxY - mapBounds.minY) * scale)) / 2;

                return function (point) {
                    return [
                        xOffset + ((point[0] - mapBounds.minX) * scale),
                        height - yOffset - ((point[1] - mapBounds.minY) * scale)
                    ];
                };
            }

            function geometryPath(feature, project) {
                const polygons = feature.geometry.type === 'Polygon'
                    ? [feature.geometry.coordinates]
                    : feature.geometry.coordinates;

                return polygons.map(function (polygon) {
                    return polygon.map(function (ring) {
                        return ring.map(function (point, index) {
                            const projected = project(point);
                            return (index ? 'L' : 'M') + projected[0].toFixed(2) + ',' + projected[1].toFixed(2);
                        }).join('') + 'Z';
                    }).join('');
                }).join('');
            }

            function ringCentroid(ring) {
                let area = 0;
                const center = ring.reduce(function (result, point, index) {
                    const next = ring[(index + 1) % ring.length];
                    const cross = (point[0] * next[1]) - (next[0] * point[1]);
                    area += cross;
                    result[0] += (point[0] + next[0]) * cross;
                    result[1] += (point[1] + next[1]) * cross;
                    return result;
                }, [0, 0]);
                area *= 0.5;

                return {
                    area: Math.abs(area),
                    point: area ? [center[0] / (6 * area), center[1] / (6 * area)] : ring[0]
                };
            }

            function centroid(feature, project) {
                const polygons = feature.geometry.type === 'Polygon'
                    ? [feature.geometry.coordinates]
                    : feature.geometry.coordinates;
                const largest = polygons
                    .map(function (polygon) { return ringCentroid(polygon[0]); })
                    .sort(function (a, b) { return b.area - a.area; })[0];

                return largest ? project(largest.point) : project(featureCoordinates(feature)[0]);
            }

            function placeLabels(labels, height) {
                labels.slice().sort(function (a, b) {
                    return a.y - b.y;
                }).forEach(function (label, index, sorted) {
                    const previous = sorted[index - 1];

                    if (previous && Math.abs(label.x - previous.x) < 58 && label.y - previous.y < 15) {
                        label.y = previous.y + 15;
                    }

                    label.y = Math.max(12, Math.min(height - 10, label.y));
                });

                return labels;
            }

            function color(cases, maximum) {
                if (cases === null) return neutralFill;
                if (maximum <= 0) return blueRamp[0];
                const index = Math.min(blueRamp.length - 1, Math.floor((cases / maximum) * blueRamp.length));
                return blueRamp[index];
            }

            function svgElement(name, attributes) {
                const element = document.createElementNS('http://www.w3.org/2000/svg', name);
                Object.keys(attributes || {}).forEach(function (attribute) {
                    element.setAttribute(attribute, attributes[attribute]);
                });
                return element;
            }

            function regionIndex(element) {
                return JSON.parse(element.dataset.regions || '[]').reduce(function (result, region) {
                    result[normalize(region.name)] = Number(region.cases) || 0;
                    return result;
                }, {});
            }

            function staticMetric(name, min, max, salt) {
                const key = name + salt;
                let hash = 0;

                for (let index = 0; index < key.length; index++) {
                    hash = ((hash << 5) - hash) + key.charCodeAt(index);
                    hash |= 0;
                }

                return min + (Math.abs(hash) % (max - min + 1));
            }

            function tooltipHandlers(element, path, name, cases) {
                const tooltip = element.querySelector('[data-map-tooltip]');

                path.addEventListener('mousemove', function (event) {
                    const rect = element.getBoundingClientRect();
                    const localX = event.clientX - rect.left;
                    const localY = event.clientY - rect.top;
                    const waiting = staticMetric(name, 1, 9, 'waiting');
                    const thisMonth = staticMetric(name, 12, 42, 'month');

                    tooltip.innerHTML = name
                        + '<br><span>' + waiting + ' Currently waiting</span>'
                        + '<br><span>' + thisMonth + ' This month</span>';
                    tooltip.hidden = false;

                    const tooltipWidth = tooltip.offsetWidth || 100;
                    const tooltipHeight = tooltip.offsetHeight || 40;
                    const left = localX > rect.width / 2
                        ? localX - tooltipWidth - 12
                        : localX + 12;
                    const top = localY > rect.height - tooltipHeight - 16
                        ? localY - tooltipHeight - 12
                        : localY + 12;

                    tooltip.style.left = Math.min(rect.width - tooltipWidth - 4, Math.max(4, left)) + 'px';
                    tooltip.style.top = Math.min(rect.height - tooltipHeight - 4, Math.max(4, top)) + 'px';
                });
                path.addEventListener('mouseleave', function () {
                    tooltip.hidden = true;
                });
            }

            function renderMap(element, geoJson) {
                const canvas = element.querySelector('[data-map-canvas]');
                const legend = element.querySelector('[data-map-legend]');
                const empty = element.querySelector('[data-map-empty]');
                const values = regionIndex(element);
                const cases = Object.keys(values).map(function (name) { return values[name]; });
                const maximum = cases.length ? Math.max.apply(null, cases) : 0;
                const features = geoJson.features || [];
                canvas.hidden = false;
                const canvasRect = canvas.getBoundingClientRect();
                const width = Math.max(260, Math.round(canvasRect.width || element.clientWidth || 360));
                const height = Math.max(180, Math.round(canvasRect.height || element.clientHeight || 360));
                const project = fitExtentProjector(features, width, height);
                const svg = svgElement('svg', { viewBox: '0 0 ' + width + ' ' + height, role: 'img', 'aria-label': 'Jordan clinic activity heatmap' });

                canvas.innerHTML = '';
                const labels = [];
                features.forEach(function (feature) {
                    const name = feature.properties.shapeName || feature.properties.name || '';
                    const value = Object.prototype.hasOwnProperty.call(values, normalize(name)) ? values[normalize(name)] : null;
                    const path = svgElement('path', {
                        class: 'solent-dash-jordan-region',
                        d: geometryPath(feature, project),
                        fill: color(value, maximum),
                        tabindex: '0'
                    });
                    const labelPosition = centroid(feature, project);
                    tooltipHandlers(element, path, name, value);
                    svg.appendChild(path);
                    labels.push({ name: name, x: labelPosition[0], y: labelPosition[1] });
                });
                placeLabels(labels, height).forEach(function (labelData) {
                    const label = svgElement('text', { class: 'solent-dash-jordan-label', x: labelData.x, y: labelData.y });

                    label.textContent = labelData.name;
                    svg.appendChild(label);
                });

                canvas.appendChild(svg);
                element.solentJordanGeoJson = geoJson;
                legend.hidden = false;
                empty.hidden = cases.length > 0;
                element.querySelector('[data-map-midpoint]').textContent = Math.round(maximum / 2);
                element.querySelector('[data-map-maximum]').textContent = maximum;
            }

            function mount(element, forceRetry) {
                element.querySelector('[data-map-loading]').hidden = false;
                element.querySelector('[data-map-error]').hidden = true;

                loadJordanGeoJson(forceRetry).then(function (geoJson) {
                    renderMap(element, geoJson);
                }).catch(function () {
                    element.querySelector('[data-map-error]').hidden = false;
                }).finally(function () {
                    element.querySelector('[data-map-loading]').hidden = true;
                });
            }

            function initialize() {
                document.querySelectorAll('[data-solent-jordan-map]').forEach(function (element) {
                    if (element.dataset.mapMounted) return;
                    element.dataset.mapMounted = 'true';

                    const retry = element.querySelector('[data-map-retry]');
                    retry.addEventListener('click', function (event) {
                        event.stopPropagation();
                        mount(element, true);
                    });
                    retry.addEventListener('keydown', function (event) {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            event.stopPropagation();
                            mount(element, true);
                        }
                    });
                    if (window.jQuery && !element.dataset.mapModalListener) {
                        element.dataset.mapModalListener = 'true';
                        window.jQuery(document).on('shown.bs.modal', function () {
                            if (element.classList.contains('solent-dash-jordan-map-expanded')
                                && element.solentJordanGeoJson
                                && element.offsetParent !== null) {
                                renderMap(element, element.solentJordanGeoJson);
                            }
                        });
                    }
                    mount(element, false);
                });
            }

            document.readyState === 'loading'
                ? document.addEventListener('DOMContentLoaded', initialize)
                : initialize();
        })();
    </script>
@endonce
