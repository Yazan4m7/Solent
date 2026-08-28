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
    data-regions="{{ json_encode($heatmapRegions, JSON_HEX_APOS | JSON_HEX_QUOT) }}"
    data-focus-region="{{ e((string) ($focusRegion ?? '')) }}"
    data-map-drilldown="{{ !empty($drilldown) ? 'true' : 'false' }}">
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
            font-family: var(--font-family-sans-serif, "Cairo", sans-serif);
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
            backdrop-filter: blur(8px);
            background: rgba(15, 23, 42, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.28);
            color: #ffffff;
            font-size: 11px;
            line-height: 1.35;
            min-width: 170px;
            padding: 10px 11px;
            pointer-events: none;
            position: absolute;
            z-index: 6;
        }

        .solent-dash-jordan-tooltip-title {
            display: block;
            font-size: 12px;
            font-weight: 850;
            line-height: 1.3;
            margin-bottom: 8px;
        }

        .solent-dash-jordan-tooltip-stats {
            display: grid;
            gap: 6px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .solent-dash-jordan-tooltip-stat {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            display: grid;
            gap: 2px;
            padding: 6px 7px;
        }

        .solent-dash-jordan-tooltip-stat small {
            color: #cbd5e1;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .solent-dash-jordan-tooltip-stat strong {
            color: #ffffff;
            font-size: 13px;
            font-weight: 850;
        }

        .solent-dash-jordan-map-expanded {
            min-height: 100%;
        }

        .solent-dash-jordan-map-expanded .solent-dash-jordan-label {
            font-size: 9px;
        }

        .solent-dash-jordan-map-focus .solent-dash-jordan-canvas {
            height: 100%;
            min-height: 320px;
        }

        .solent-dash-jordan-map-focus .solent-dash-jordan-label {
            fill: #0f172a;
            font-size: 20px;
            font-weight: 900;
            stroke-width: 5px;
        }

        .solent-dash-jordan-map-focus .solent-dash-jordan-region {
            stroke-width: 1.7;
        }

        .solent-dash-jordan-adm2-region {
            cursor: pointer;
            pointer-events: auto;
            stroke: #ffffff;
            stroke-linejoin: round;
            stroke-opacity: 0.96;
            stroke-width: 1.7;
            transition: filter 140ms ease, opacity 140ms ease, stroke 140ms ease, stroke-width 140ms ease;
            vector-effect: non-scaling-stroke;
        }

        .solent-dash-jordan-adm2-region:hover,
        .solent-dash-jordan-adm2-region:focus {
            filter: brightness(0.82) saturate(1.08);
            opacity: 1;
            outline: none;
            stroke: #0f172a;
            stroke-width: 2.3;
        }

        .solent-dash-jordan-map-focus .solent-dash-jordan-legend {
            display: none;
        }

        .solent-dash-jordan-area-label {
            fill: #0f172a;
            font-family: var(--font-family-sans-serif, "Cairo", sans-serif);
            font-size: 12px;
            font-weight: 850;
            paint-order: stroke;
            pointer-events: none;
            stroke: rgba(255, 255, 255, 0.92);
            stroke-linejoin: round;
            stroke-width: 3.5px;
            text-anchor: middle;
        }

        @media (max-width: 760px) {
            .solent-dash-jordan-map-focus .solent-dash-jordan-area-label {
                font-size: 11px;
                stroke-width: 3px;
            }
        }

        @keyframes solentJordanPulse {
            0%, 100% { opacity: 0.58; }
            50% { opacity: 1; }
        }
    </style>

    <script>
        (function () {
            const adm1MetadataUrl = 'https://www.geoboundaries.org/api/current/gbOpen/JOR/ADM1/';
            const adm2MetadataUrl = 'https://www.geoboundaries.org/api/current/gbOpen/JOR/ADM2/';
            const neutralFill = '#cbd5e1';
            const blueRamp = ['#f4f8fc', '#d8e8f3', '#a8cce1', '#609dc3', '#1a6fa8'];
            const aliases = {
                ajlun: 'ajloun',
                albalqa: 'salt',
                albalqaa: 'salt',
                alkarak: 'karak',
                assalt: 'salt',
                attafilah: 'tafilah',
                azzarqa: 'zarqa',
                balqa: 'salt',
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
                const preferredUrl = mediaUrl(url);

                return fetch(preferredUrl).then(function (response) {
                    if (!response.ok) throw new Error('GeoJSON download failed.');
                    return parseGeoJson(response);
                }).catch(function (error) {
                    if (preferredUrl === url) throw error;

                    return fetch(url).then(function (response) {
                        if (!response.ok) throw new Error('GeoJSON media download failed.');
                        return parseGeoJson(response);
                    });
                });
            }

            function loadGeoJsonLayer(metadataUrl, promiseKey, forceRetry) {
                if (forceRetry || !window[promiseKey]) {
                    window[promiseKey] = fetch(metadataUrl)
                        .then(function (response) {
                            if (!response.ok) throw new Error('GeoJSON metadata failed.');
                            return response.json();
                        })
                        .then(function (metadata) {
                            return fetchGeoJson(metadata.simplifiedGeometryGeoJSON || metadata.gjDownloadURL);
                        })
                        .catch(function (error) {
                            window[promiseKey] = null;
                            throw error;
                        });
                }

                return window[promiseKey];
            }

            function loadJordanGeoJson(forceRetry) {
                return loadGeoJsonLayer(adm1MetadataUrl, 'solentJordanGeoJsonPromise', forceRetry);
            }

            function loadJordanAdm2GeoJson(forceRetry) {
                return loadGeoJsonLayer(adm2MetadataUrl, 'solentJordanAdm2GeoJsonPromise', forceRetry);
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

            function representativePoint(feature) {
                const polygons = feature.geometry.type === 'Polygon'
                    ? [feature.geometry.coordinates]
                    : feature.geometry.coordinates;
                const largest = polygons
                    .map(function (polygon) { return ringCentroid(polygon[0]); })
                    .sort(function (a, b) { return b.area - a.area; })[0];

                return largest ? largest.point : featureCoordinates(feature)[0];
            }

            function centroid(feature, project) {
                return project(representativePoint(feature));
            }

            function pointInRing(point, ring) {
                let inside = false;

                for (let index = 0, previous = ring.length - 1; index < ring.length; previous = index++) {
                    const currentPoint = ring[index];
                    const previousPoint = ring[previous];
                    const crossesLatitude = (currentPoint[1] > point[1]) !== (previousPoint[1] > point[1]);
                    const crossingLongitude = ((previousPoint[0] - currentPoint[0]) * (point[1] - currentPoint[1]))
                        / ((previousPoint[1] - currentPoint[1]) || Number.EPSILON)
                        + currentPoint[0];

                    if (crossesLatitude && point[0] < crossingLongitude) {
                        inside = !inside;
                    }
                }

                return inside;
            }

            function pointInFeature(point, feature) {
                const polygons = feature.geometry.type === 'Polygon'
                    ? [feature.geometry.coordinates]
                    : feature.geometry.coordinates;

                return polygons.some(function (polygon) {
                    if (!polygon.length || !pointInRing(point, polygon[0])) return false;

                    return !polygon.slice(1).some(function (hole) {
                        return pointInRing(point, hole);
                    });
                });
            }

            function featureBelongsToGovernorate(feature, governorateFeature) {
                if (pointInFeature(representativePoint(feature), governorateFeature)) {
                    return true;
                }

                const coordinates = featureCoordinates(feature);
                const sampleStep = Math.max(1, Math.ceil(coordinates.length / 24));

                return coordinates.some(function (point, index) {
                    return index % sampleStep === 0 && pointInFeature(point, governorateFeature);
                });
            }

            function adm2FeaturesForGovernorate(adm2GeoJson, governorateFeature) {
                if (!adm2GeoJson || !governorateFeature) return [];

                return (adm2GeoJson.features || []).filter(function (feature) {
                    return featureBelongsToGovernorate(feature, governorateFeature);
                });
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

            function featureDisplayName(feature) {
                return String((feature.properties || {}).shapeName || (feature.properties || {}).name || 'Area');
            }

            function focusRegionName(element) {
                return normalize(element.dataset.focusRegion || '');
            }

            function isDrilldownEnabled(element) {
                return element.dataset.mapDrilldown === 'true';
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

            function escapeHtml(value) {
                return String(value).replace(/[&<>"']/g, function (character) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    }[character];
                });
            }

            function tooltipPosition(element, tooltip, event) {
                const rect = element.getBoundingClientRect();
                const localX = event.clientX - rect.left;
                const localY = event.clientY - rect.top;
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
            }

            function tooltipPositionForNode(element, tooltip, node) {
                const nodeRect = node.getBoundingClientRect();

                tooltipPosition(element, tooltip, {
                    clientX: nodeRect.left + (nodeRect.width / 2),
                    clientY: nodeRect.top + (nodeRect.height / 2)
                });
            }

            function areaMetrics(areaName, regionCases) {
                const completedCeiling = Math.max(12, Math.min(96, Math.round((regionCases || 24) * 0.28)));
                const completedFloor = Math.max(4, Math.min(24, Math.round(completedCeiling * 0.4)));
                const completed = staticMetric(areaName, completedFloor, completedCeiling, 'completed');
                const waiting = staticMetric(areaName, 1, Math.max(3, Math.min(18, Math.round(completed * 0.45))), 'waiting');

                return { waiting: waiting, completed: completed };
            }

            function tooltipMarkup(name, firstLabel, firstValue, secondLabel, secondValue) {
                return '<strong class="solent-dash-jordan-tooltip-title">' + escapeHtml(name) + '</strong>'
                    + '<span class="solent-dash-jordan-tooltip-stats">'
                    + '<span class="solent-dash-jordan-tooltip-stat"><small>' + escapeHtml(firstLabel) + '</small><strong>' + Number(firstValue || 0).toLocaleString() + '</strong></span>'
                    + '<span class="solent-dash-jordan-tooltip-stat"><small>' + escapeHtml(secondLabel) + '</small><strong>' + Number(secondValue || 0).toLocaleString() + '</strong></span>'
                    + '</span>';
            }

            function tooltipHandlers(element, path, name, cases) {
                const tooltip = element.querySelector('[data-map-tooltip]');

                path.addEventListener('mousemove', function (event) {
                    const waiting = staticMetric(name, 1, 9, 'waiting');
                    const thisMonth = staticMetric(name, 12, 42, 'month');

                    tooltip.innerHTML = tooltipMarkup(name, 'Waiting', waiting, 'This month', thisMonth);
                    tooltip.hidden = false;
                    tooltipPosition(element, tooltip, event);
                });
                path.addEventListener('mouseleave', function () {
                    tooltip.hidden = true;
                });
                path.addEventListener('focus', function () {
                    const waiting = staticMetric(name, 1, 9, 'waiting');
                    const thisMonth = staticMetric(name, 12, 42, 'month');

                    tooltip.innerHTML = tooltipMarkup(name, 'Waiting', waiting, 'This month', thisMonth);
                    tooltip.hidden = false;
                    tooltipPositionForNode(element, tooltip, path);
                });
                path.addEventListener('blur', function () {
                    tooltip.hidden = true;
                });
            }

            function areaTooltipHandlers(element, node, areaName, metrics) {
                const tooltip = element.querySelector('[data-map-tooltip]');

                function show(event) {
                    tooltip.innerHTML = tooltipMarkup(areaName, 'Waiting', metrics.waiting, 'Completed', metrics.completed);
                    tooltip.hidden = false;

                    if (event) {
                        tooltipPosition(element, tooltip, event);
                    } else {
                        tooltipPositionForNode(element, tooltip, node);
                    }
                }

                node.addEventListener('mousemove', function (event) {
                    show(event);
                });
                node.addEventListener('mouseleave', function () {
                    tooltip.hidden = true;
                });
                node.addEventListener('focus', function () {
                    show();
                });
                node.addEventListener('blur', function () {
                    tooltip.hidden = true;
                });
            }

            function dispatchRegionSelected(element, name, cases) {
                element.dispatchEvent(new CustomEvent('solent:jordan-region-selected', {
                    bubbles: true,
                    detail: {
                        name: name,
                        normalizedName: normalize(name),
                        cases: cases === null ? 0 : cases
                    }
                }));
            }

            function renderMap(element, geoJson, adm2GeoJson) {
                const canvas = element.querySelector('[data-map-canvas]');
                const legend = element.querySelector('[data-map-legend]');
                const empty = element.querySelector('[data-map-empty]');
                const values = regionIndex(element);
                const cases = Object.keys(values).map(function (name) { return values[name]; });
                const maximum = cases.length ? Math.max.apply(null, cases) : 0;
                const allFeatures = geoJson.features || [];
                const focusRegion = focusRegionName(element);
                const filteredFeatures = focusRegion
                    ? allFeatures.filter(function (feature) {
                        const featureName = feature.properties.shapeName || feature.properties.name || '';
                        return normalize(featureName) === focusRegion;
                    })
                    : allFeatures;
                const features = filteredFeatures.length ? filteredFeatures : allFeatures;
                const isFocusedView = !!focusRegion && filteredFeatures.length > 0;
                const focusedAdm2Features = isFocusedView
                    ? adm2FeaturesForGovernorate(adm2GeoJson, filteredFeatures[0])
                    : [];
                const focusedAreaRows = focusedAdm2Features.map(function (feature) {
                    const name = featureDisplayName(feature);
                    const metrics = areaMetrics(name, values[focusRegion] || 0);

                    return {
                        feature: feature,
                        name: name,
                        metrics: metrics,
                        value: metrics.waiting + metrics.completed
                    };
                });
                const focusedAreaMaximum = focusedAreaRows.length
                    ? Math.max.apply(null, focusedAreaRows.map(function (area) { return area.value; }))
                    : 0;
                canvas.hidden = false;
                element.classList.toggle('solent-dash-jordan-map-focus', isFocusedView);
                const isExpandedView = element.classList.contains('solent-dash-jordan-map-expanded');
                const width = isExpandedView ? 720 : 360;
                const height = isExpandedView ? 420 : 180;
                const project = fitExtentProjector(features, width, height);
                const svg = svgElement('svg', {
                    viewBox: '0 0 ' + width + ' ' + height,
                    preserveAspectRatio: isExpandedView ? 'xMidYMid meet' : 'xMidYMid slice',
                    role: 'img',
                    'aria-label': 'Jordan clinic activity heatmap'
                });
                const defs = svgElement('defs');

                canvas.innerHTML = '';
                svg.appendChild(defs);
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
                    if (isDrilldownEnabled(element)) {
                        path.addEventListener('click', function (event) {
                            event.preventDefault();
                            event.stopPropagation();
                            dispatchRegionSelected(element, name, value);
                        });
                        path.addEventListener('keydown', function (event) {
                            if (event.key === 'Enter' || event.key === ' ') {
                                event.preventDefault();
                                event.stopPropagation();
                                dispatchRegionSelected(element, name, value);
                            }
                        });
                    }
                    svg.appendChild(path);
                    labels.push({ name: name, x: labelPosition[0], y: labelPosition[1] });
                });

                if (focusedAdm2Features.length) {
                    const adm2ClipId = 'solent-jordan-adm2-clip-' + focusRegion;
                    const adm2Clip = svgElement('clipPath', { id: adm2ClipId });
                    const adm2Layer = svgElement('g', {
                        class: 'solent-dash-jordan-adm2-layer',
                        'clip-path': 'url(#' + adm2ClipId + ')'
                    });
                    const areaLabels = [];

                    adm2Clip.appendChild(svgElement('path', {
                        d: geometryPath(filteredFeatures[0], project)
                    }));
                    defs.appendChild(adm2Clip);

                    focusedAreaRows.forEach(function (area) {
                        const path = svgElement('path', {
                            class: 'solent-dash-jordan-adm2-region',
                            d: geometryPath(area.feature, project),
                            fill: color(area.value, focusedAreaMaximum),
                            role: 'img',
                            tabindex: '0',
                            'aria-label': area.name + ': ' + area.metrics.waiting + ' waiting, ' + area.metrics.completed + ' completed'
                        });
                        const labelPosition = centroid(area.feature, project);

                        areaTooltipHandlers(element, path, area.name, area.metrics);
                        adm2Layer.appendChild(path);
                        areaLabels.push({ name: area.name, x: labelPosition[0], y: labelPosition[1] });
                    });
                    svg.appendChild(adm2Layer);

                    placeLabels(areaLabels, height).forEach(function (areaLabel) {
                        const label = svgElement('text', {
                            class: 'solent-dash-jordan-area-label',
                            x: areaLabel.x,
                            y: areaLabel.y
                        });

                        label.textContent = areaLabel.name;
                        svg.appendChild(label);
                    });
                }

                if (!isFocusedView) {
                    placeLabels(labels, height).forEach(function (labelData) {
                        const label = svgElement('text', { class: 'solent-dash-jordan-label', x: labelData.x, y: labelData.y });

                        label.textContent = labelData.name;
                        svg.appendChild(label);
                    });
                }

                canvas.appendChild(svg);
                element.solentJordanGeoJson = geoJson;
                element.solentJordanAdm2GeoJson = adm2GeoJson || element.solentJordanAdm2GeoJson || null;
                legend.hidden = isFocusedView || cases.length === 0;
                empty.hidden = cases.length > 0;
                element.querySelector('[data-map-midpoint]').textContent = Math.round(maximum / 2);
                element.querySelector('[data-map-maximum]').textContent = maximum;
            }

            function mount(element, forceRetry) {
                element.querySelector('[data-map-loading]').hidden = false;
                element.querySelector('[data-map-error]').hidden = true;

                const adm2Promise = focusRegionName(element)
                    ? loadJordanAdm2GeoJson(forceRetry).catch(function () { return null; })
                    : Promise.resolve(element.solentJordanAdm2GeoJson || null);

                Promise.all([loadJordanGeoJson(forceRetry), adm2Promise]).then(function (layers) {
                    renderMap(element, layers[0], layers[1]);
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
                    mount(element, false);
                });
            }

            window.solentJordanMapRerender = function (element, forceRetry) {
                if (!element) return;
                const needsAdm2 = !!focusRegionName(element);
                if (
                    element.solentJordanGeoJson &&
                    (!needsAdm2 || element.solentJordanAdm2GeoJson) &&
                    !forceRetry
                ) {
                    renderMap(element, element.solentJordanGeoJson, element.solentJordanAdm2GeoJson);
                    return;
                }

                mount(element, !!forceRetry);
            };

            document.readyState === 'loading'
                ? document.addEventListener('DOMContentLoaded', initialize)
                : initialize();
        })();
    </script>
@endonce
