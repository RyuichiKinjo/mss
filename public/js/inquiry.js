(function () {
    // プロフィールページのURLは仮。本番のURLが決まり次第差し替える。
    var PROFILE_PAGE_URL = 'https://example.com/profile';

    var dataNode = document.getElementById('career-data');
    var skillNode = document.getElementById('skill-summary-data');
    var profileNode = document.getElementById('profile-display');
    var root = document.getElementById('app');
    if (!dataNode || !skillNode || !profileNode || !root) {
        return;
    }

    var career = JSON.parse(dataNode.textContent);
    var skillSummary = JSON.parse(skillNode.textContent);
    var profileDisplay = JSON.parse(profileNode.textContent);
    var editorHandler = null;
    render(career, skillSummary);
    bind();

    function render(career, skillSummary) {
        var profile = career.profile || {};
        root.appendChild(renderHeader());
        var sheet = el('main', 'sheet');
        sheet.appendChild(renderProfile(profile));
        sheet.appendChild(renderSkills(skillSummary));
        sheet.appendChild(renderProjects(career.projects || []));
        root.appendChild(sheet);
        root.appendChild(renderModal());
        root.appendChild(renderEditorModal());
    }

    function renderHeader() {
        var bar = el('header', 'topbar');
        var inner = el('div', 'topbar-inner');
        var brand = el('a', 'brand', 'MY SKILL SHEETS');
        brand.href = '/';
        var nav = el('nav', 'topnav');
        nav.setAttribute('aria-label', 'セクション');
        [
            ['#basic', '基本情報'],
            ['#skills', 'スキル要約'],
            ['#career', '業務経歴']
        ].forEach(function (item) {
            var link = el('a', null, item[1]);
            link.href = item[0];
            nav.appendChild(link);
        });
        var actions = el('div', 'topbar-actions');
        actions.appendChild(button('経歴書PDF', 'btn btn-ghost', 'pdf-career'));
        actions.appendChild(button('スキル要約PDF', 'btn btn-ghost', 'pdf-summary'));
        if (profileDisplay.admin) {
            actions.appendChild(button('管理終了', 'btn btn-solid', 'admin-end'));
        } else {
            actions.appendChild(button('管理', 'btn btn-solid', 'admin-open'));
        }
        inner.appendChild(brand);
        inner.appendChild(nav);
        inner.appendChild(actions);
        bar.appendChild(inner);
        return bar;
    }

    function renderProfile(profile) {
        var section = el('section', 'panel');
        section.id = 'basic';
        section.appendChild(sectionHead('基本情報', '更新', 'profile'));
        var dl = el('dl', 'profile');
        addPair(dl, '氏名', profile.name || '');
        addPair(dl, 'フリガナ', profile.nameKana || '');
        addPair(dl, '生年月日', profileDisplay.birthday || '');
        addPair(dl, '職種', profile.role || '');
        addLink(dl, 'プロフィールページ', profile.profileUrl || PROFILE_PAGE_URL);
        var dt = el('dt', null, '資格');
        var dd = el('dd');
        var list = el('ul', 'certs');
        (profile.qualifications || []).forEach(function (name) {
            list.appendChild(el('li', null, name));
        });
        dd.appendChild(list);
        dl.appendChild(dt);
        dl.appendChild(dd);
        section.appendChild(dl);
        return section;
    }

    function renderSkills(summary) {
        var section = el('section', 'panel');
        section.id = 'skills';
        section.appendChild(el('h2', null, 'スキル要約'));

        var privateGroup = summary.private || {};
        section.appendChild(el('h3', 'skill-heading', privateGroup.label || 'プライベートでの使用技術'));
        section.appendChild(skillGrid(privateGroup.groups || [], 'private'));

        var business = summary.business || {};
        section.appendChild(el('h3', 'skill-heading', business.label || '業務での使用技術'));
        section.appendChild(skillGrid(business.groups || [], 'business'));
        return section;
    }

    function skillGrid(groups, side) {
        var grid = el('div', 'skill-grid');
        groups.forEach(function (group) {
            var block = el('div', 'skill-block');
            block.appendChild(skillTable(group.skills || [], group.label || '', side));
            if (profileDisplay.admin) {
                var add = button('追加', 'btn btn-line btn-small', null);
                add.setAttribute('data-edit', 'skill');
                add.dataset.side = side;
                add.dataset.group = group.label || '';
                block.appendChild(add);
            }
            grid.appendChild(block);
        });
        return grid;
    }

    function skillTable(skills, heading, side) {
        var table = el('table');
        var thead = el('thead');
        thead.appendChild(row('th', [heading]));
        var tbody = el('tbody');
        skills.forEach(function (skill) {
            var name = skill.name || '';
            if (skill.area) {
                name += '（' + skill.area + '）';
            }
            var tr = el('tr');
            var td = el('td');
            var line = el('div', 'skill-row');
            var label = el('span', 'skill-item', name);
            if (skill.level) {
                label.appendChild(el('span', 'level-badge', skill.level));
            }
            line.appendChild(label);
            if (profileDisplay.admin) {
                var actions = el('div', 'skill-actions');
                actions.appendChild(skillAction('更新', 'edit', side, heading, skill.name || ''));
                actions.appendChild(skillAction('削除', 'delete', side, heading, skill.name || ''));
                line.appendChild(actions);
            }
            td.appendChild(line);
            tr.appendChild(td);
            tbody.appendChild(tr);
        });
        table.appendChild(thead);
        table.appendChild(tbody);
        return table;
    }

    function skillAction(label, action, side, group, name) {
        var node = button(label, 'btn btn-line btn-small', null);
        node.setAttribute(action === 'delete' ? 'data-delete' : 'data-edit', 'skill');
        node.dataset.side = side;
        node.dataset.group = group;
        node.dataset.name = name;
        return node;
    }

    function renderProjects(projects) {
        var section = el('section', 'panel');
        section.id = 'career';
        section.appendChild(sectionHead('業務経歴', '経歴を追加', 'project'));

        var searchRow = el('div', 'search-row');
        var input = el('input');
        input.id = 'keyword';
        input.type = 'search';
        input.placeholder = 'キーワードで絞り込み（例: Java 金融）';
        input.autocomplete = 'off';
        var count = el('span', null, '');
        count.id = 'count';
        searchRow.appendChild(input);
        searchRow.appendChild(count);
        section.appendChild(searchRow);

        var table = el('table', 'projects');
        var thead = el('thead');
        var headers = ['No.', '期間', '業務内容', '役割・規模', '使用技術', '担当工程'];
        if (profileDisplay.admin) {
            headers.push('操作');
        }
        thead.appendChild(row('th', headers));
        var tbody = el('tbody');
        tbody.id = 'projects';

        sortProjects(projects).forEach(function (project) {
            tbody.appendChild(renderProject(project));
        });

        table.appendChild(thead);
        table.appendChild(tbody);
        section.appendChild(table);

        var empty = el('p', 'empty', '該当する経歴がありません');
        empty.id = 'empty';
        empty.hidden = true;
        section.appendChild(empty);
        return section;
    }

    function renderProject(project) {
        var period = formatPeriod(project.period);
        var tech = project.technologies || {};
        var fields = {
            period: period.label,
            industry: project.industry || '',
            title: project.title || '',
            description: project.description || '',
            role: project.role || '',
            team: project.teamSize || '',
            phases: joinList(project.phases),
            languages: joinList(tech.languages),
            frameworks: joinList(tech.frameworks),
            databases: joinList(tech.databases),
            environment: joinList(tech.environment),
            tools: joinList(tech.tools)
        };

        var tr = el('tr', 'project');
        tr.dataset.search = [
            fields.period, fields.industry, fields.title, fields.description,
            fields.role, fields.team, fields.phases, fields.languages,
            fields.frameworks, fields.databases, fields.environment, fields.tools
        ].join(' ');

        tr.appendChild(cell('No.', 'col-no js-no', ''));
        tr.appendChild(cell('期間', 'col-period', fields.period, true));

        var work = cell('業務内容', 'col-work');
        if (fields.industry) {
            work.appendChild(hl('div', 'industry', fields.industry));
        }
        work.appendChild(hl('div', 'title', fields.title));
        if (fields.description) {
            work.appendChild(hl('div', 'desc', fields.description));
        }
        tr.appendChild(work);

        var role = cell('役割・規模', 'col-role');
        if (fields.role) {
            role.appendChild(hl('div', null, fields.role));
        }
        if (fields.team) {
            role.appendChild(hl('div', 'team', fields.team));
        }
        tr.appendChild(role);

        var techCell = cell('使用技術', 'col-tech');
        [
            ['言語', fields.languages],
            ['FW', fields.frameworks],
            ['DB', fields.databases],
            ['環境', fields.environment],
            ['ツール', fields.tools]
        ].forEach(function (pair) {
            if (!pair[1]) {
                return;
            }
            var line = el('div', 'tech-line');
            line.appendChild(el('span', 'k', pair[0]));
            line.appendChild(hl('span', null, pair[1]));
            techCell.appendChild(line);
        });
        tr.appendChild(techCell);
        tr.appendChild(cell('担当工程', 'col-phase', fields.phases, true));
        if (profileDisplay.admin) {
            var actions = cell('操作', 'col-actions');
            var wrap = el('div', 'row-actions');
            var update = button('更新', 'btn btn-line btn-small', null);
            update.setAttribute('data-edit', 'project');
            update.dataset.id = project.id || '';
            var remove = button('削除', 'btn btn-line btn-small', null);
            remove.setAttribute('data-delete', 'project');
            remove.dataset.id = project.id || '';
            wrap.appendChild(update);
            wrap.appendChild(remove);
            actions.appendChild(wrap);
            tr.appendChild(actions);
        }
        return tr;
    }

    function renderModal() {
        var modal = el('div', 'modal');
        modal.id = 'admin-modal';
        modal.hidden = true;
        var card = el('div', 'modal-card');
        card.setAttribute('role', 'dialog');
        card.setAttribute('aria-modal', 'true');
        card.setAttribute('aria-labelledby', 'admin-title');
        var title = el('h3', null, '管理');
        title.id = 'admin-title';
        card.appendChild(title);
        card.appendChild(el('p', 'modal-note', 'パスワードが一致すると管理モードになります。'));
        var form = el('form');
        var label = el('label', null, 'パスワード');
        label.htmlFor = 'admin-password';
        var input = el('input');
        input.id = 'admin-password';
        input.type = 'password';
        input.autocomplete = 'current-password';
        var error = el('p', 'modal-error', '');
        error.id = 'admin-error';
        error.hidden = true;
        form.appendChild(label);
        form.appendChild(input);
        form.appendChild(error);
        var actions = el('div', 'modal-actions');
        var cancel = button('キャンセル', 'btn btn-line', null);
        cancel.setAttribute('data-close-modal', '');
        var submit = button('確認', 'btn btn-primary', 'admin-submit');
        submit.type = 'submit';
        actions.appendChild(cancel);
        actions.appendChild(submit);
        form.appendChild(actions);
        card.appendChild(form);
        modal.appendChild(card);
        return modal;
    }

    function renderEditorModal() {
        var modal = el('div', 'modal');
        modal.id = 'editor-modal';
        modal.hidden = true;
        var card = el('div', 'modal-card editor-card');
        card.setAttribute('role', 'dialog');
        card.setAttribute('aria-modal', 'true');
        card.setAttribute('aria-labelledby', 'editor-title');
        var title = el('h3', null, '');
        title.id = 'editor-title';
        var form = el('form');
        form.id = 'editor-form';
        var fields = el('div');
        fields.id = 'editor-fields';
        var error = el('p', 'modal-error', '');
        error.id = 'editor-error';
        error.hidden = true;
        var actions = el('div', 'modal-actions');
        var cancel = button('キャンセル', 'btn btn-line', null);
        cancel.setAttribute('data-close-editor', '');
        var submit = button('保存', 'btn btn-primary', 'editor-submit');
        submit.type = 'submit';
        actions.appendChild(cancel);
        actions.appendChild(submit);
        form.appendChild(fields);
        form.appendChild(error);
        form.appendChild(actions);
        card.appendChild(title);
        card.appendChild(form);
        modal.appendChild(card);
        return modal;
    }

    function sectionHead(title, actionLabel, kind) {
        if (!profileDisplay.admin) {
            return el('h2', null, title);
        }
        var head = el('div', 'section-head');
        head.appendChild(el('h2', null, title));
        var action = button(actionLabel, 'btn btn-line btn-small', null);
        action.setAttribute('data-edit', kind);
        head.appendChild(action);
        return head;
    }

    function bind() {
        var input = document.getElementById('keyword');
        var rows = Array.prototype.slice.call(document.querySelectorAll('tr.project'));
        var empty = document.getElementById('empty');
        var count = document.getElementById('count');
        var modal = document.getElementById('admin-modal');

        rows.forEach(function (row) {
            Array.prototype.forEach.call(row.querySelectorAll('.js-hl'), function (node) {
                node.dataset.original = node.textContent;
            });
        });

        function apply() {
            var terms = input.value.trim().toLowerCase().split(/\s+/).filter(Boolean);
            var visible = 0;
            rows.forEach(function (row) {
                var hay = (row.dataset.search || '').toLowerCase();
                var show = terms.every(function (term) { return hay.indexOf(term) !== -1; });
                row.hidden = !show;
                if (show) {
                    visible += 1;
                    row.querySelector('.js-no').textContent = String(visible);
                }
                Array.prototype.forEach.call(row.querySelectorAll('.js-hl'), function (node) {
                    node.innerHTML = highlight(node.dataset.original || '', terms);
                });
            });
            count.textContent = visible + '件';
            empty.hidden = visible !== 0;
        }

        input.addEventListener('input', apply);
        apply();

        document.getElementById('pdf-career').addEventListener('click', function () {
            window.location.href = '/pdf/career';
        });

        document.getElementById('pdf-summary').addEventListener('click', function () {
            window.location.href = '/pdf/summary';
        });

        var adminOpen = document.getElementById('admin-open');
        if (adminOpen) {
            adminOpen.addEventListener('click', function () {
                modal.hidden = false;
                document.getElementById('admin-password').focus();
            });
            document.getElementById('admin-modal').querySelector('form').addEventListener('submit', submitAdmin);
        }
        var adminEnd = document.getElementById('admin-end');
        if (adminEnd) {
            adminEnd.addEventListener('click', function () {
                adminEnd.disabled = true;
                postJson('/admin/lock', {}).then(function (response) {
                    if (response.ok) {
                        window.location.reload();
                        return;
                    }
                    adminEnd.disabled = false;
                });
            });
        }
        Array.prototype.forEach.call(document.querySelectorAll('[data-close-modal]'), function (node) {
            node.addEventListener('click', closeModal);
        });
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });
        document.getElementById('editor-form').addEventListener('submit', function (event) {
            event.preventDefault();
            if (editorHandler) {
                editorHandler(new FormData(event.target));
            }
        });
        Array.prototype.forEach.call(document.querySelectorAll('[data-close-editor]'), function (node) {
            node.addEventListener('click', closeEditor);
        });
        root.addEventListener('click', onAdminClick);
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !modal.hidden) {
                closeModal();
            }
        });

        function closeModal() {
            modal.hidden = true;
            var error = document.getElementById('admin-error');
            var input = document.getElementById('admin-password');
            if (error) {
                error.hidden = true;
                error.textContent = '';
            }
            if (input) {
                input.value = '';
            }
        }

        function submitAdmin(event) {
            event.preventDefault();
            var input = document.getElementById('admin-password');
            var error = document.getElementById('admin-error');
            var submit = document.getElementById('admin-submit');
            submit.disabled = true;
            error.hidden = true;
            postJson('/admin/unlock', { password: input.value }).then(function (response) {
                if (response.ok) {
                    window.location.reload();
                    return;
                }
                if (response.status === 429) {
                    error.textContent = 'しばらくしてからもう一度入力してください';
                    error.hidden = false;
                    submit.disabled = false;
                    return;
                }
                return response.json().then(function (body) {
                    error.textContent = body.message || 'パスワードが違います';
                    error.hidden = false;
                    submit.disabled = false;
                    input.focus();
                }).catch(function () {
                    error.textContent = 'パスワードが違います';
                    error.hidden = false;
                    submit.disabled = false;
                });
            });
        }

        function postJson(url, body) {
            return sendJson(url, 'POST', body);
        }
    }

    function sendJson(url, method, body) {
        return fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(body)
        });
    }

    function onAdminClick(event) {
        if (!profileDisplay.admin) {
            return;
        }
        var edit = event.target.closest('[data-edit]');
        var remove = event.target.closest('[data-delete]');
        var node = edit || remove;
        if (!node || !root.contains(node)) {
            return;
        }
        if (remove) {
            if (remove.dataset.delete === 'skill') {
                deleteSkill(remove.dataset.side, remove.dataset.group, remove.dataset.name);
            } else if (remove.dataset.delete === 'project') {
                deleteProject(remove.dataset.id);
            }
            return;
        }
        if (edit.dataset.edit === 'profile') {
            editProfile();
        } else if (edit.dataset.edit === 'skill') {
            var skill = edit.dataset.name ? findSkill(edit.dataset.side, edit.dataset.group, edit.dataset.name) : null;
            editSkill(edit.dataset.side, edit.dataset.group || '', skill);
        } else if (edit.dataset.edit === 'project') {
            editProject(edit.dataset.id ? findProject(edit.dataset.id) : null);
        }
    }

    function editProfile() {
        var profile = career.profile || {};
        openEditor('基本情報', [
            field('氏名', 'name', profile.name || ''),
            field('フリガナ', 'nameKana', profile.nameKana || ''),
            field('生年月日', 'birthday', profile.birthday || '1982-08-05', 'date'),
            field('職種', 'role', profile.role || ''),
            field('プロフィールページ', 'profileUrl', profile.profileUrl || PROFILE_PAGE_URL, 'url'),
            field('資格（1行に1つ）', 'qualifications', (profile.qualifications || []).join('\n'), 'textarea')
        ], function (data) {
            saveEditor('/admin/profile', 'PUT', {
                name: data.get('name'),
                nameKana: data.get('nameKana'),
                birthday: data.get('birthday'),
                role: data.get('role'),
                profileUrl: data.get('profileUrl'),
                qualifications: lines(data.get('qualifications'))
            });
        });
    }

    function editSkill(side, groupLabel, skill) {
        var groups = ((skillSummary[side] || {}).groups || []).map(function (group) {
            return group.label || '';
        }).filter(Boolean);
        var groupSelect = selectField('グループ', 'group', groupLabel, groups);
        var newGroup = field('新しいグループ名', 'newGroup', '');
        var level = field('レベル', 'level', skill ? (skill.level || '') : '');
        var area = field('区分', 'area', skill ? (skill.area || '') : '');
        var select = groupSelect.querySelector('select');
        var newGroupInput = newGroup.querySelector('input');
        function syncGroup() {
            var group = select.value === '__new__' ? String(newGroupInput.value || '').trim() : select.value;
            newGroup.hidden = select.value !== '__new__';
            var showLevel = side === 'business' && group === '言語';
            var showArea = side === 'business' && group === 'フレームワーク';
            level.hidden = !showLevel;
            area.hidden = !showArea;
        }
        select.addEventListener('change', syncGroup);
        newGroupInput.addEventListener('input', syncGroup);
        syncGroup();
        var fields = [groupSelect, newGroup, field('技術名', 'name', skill ? (skill.name || '') : '')];
        if (side === 'business') {
            fields.push(level, area);
        }
        openEditor(skill ? '技術を更新' : '技術を追加', fields, function (data) {
            var group = data.get('group') === '__new__' ? String(data.get('newGroup') || '').trim() : data.get('group');
            var body = {
                side: side,
                group: group,
                name: String(data.get('name') || '').trim()
            };
            if (side === 'business' && group === '言語') {
                body.level = String(data.get('level') || '').trim();
            }
            if (side === 'business' && group === 'フレームワーク') {
                body.area = String(data.get('area') || '').trim();
            }
            if (skill) {
                body.fromGroup = groupLabel;
                body.fromName = skill.name || '';
            }
            saveEditor('/admin/skills', skill ? 'PUT' : 'POST', body);
        });
    }

    function editProject(project) {
        var period = periodParts(project ? project.period : '');
        var tech = (project && project.technologies) || {};
        var note = el('p', 'modal-note', '業務での使用技術に同じ名前が無い技術は、保存すると追加されます。レベルなどはあとから調整できます。');
        openEditor(project ? '経歴を更新' : '経歴を追加', [
            note,
            field('プロジェクト名', 'title', project ? (project.title || '') : ''),
            field('開始年月', 'start', period.start, 'month'),
            field('終了年月（空なら継続）', 'end', period.end, 'month'),
            field('業種', 'industry', project ? (project.industry || '') : ''),
            field('概要', 'description', project ? (project.description || '') : '', 'textarea'),
            field('役割', 'role', project ? (project.role || '') : ''),
            field('規模', 'teamSize', project ? (project.teamSize || '') : ''),
            field('言語（1行に1つ）', 'languages', listText(tech.languages), 'textarea'),
            field('FW（1行に1つ）', 'frameworks', listText(tech.frameworks), 'textarea'),
            field('DB（1行に1つ）', 'databases', listText(tech.databases), 'textarea'),
            field('環境（1行に1つ）', 'environment', listText(tech.environment), 'textarea'),
            field('ツール（1行に1つ）', 'tools', listText(tech.tools), 'textarea'),
            field('担当工程（1行に1つ）', 'phases', listText(project ? project.phases : []), 'textarea')
        ], function (data) {
            saveEditor(project ? '/admin/projects/' + encodeURIComponent(project.id) : '/admin/projects', project ? 'PUT' : 'POST', {
                title: data.get('title'),
                start: data.get('start'),
                end: data.get('end'),
                industry: data.get('industry'),
                description: data.get('description'),
                role: data.get('role'),
                teamSize: data.get('teamSize'),
                languages: lines(data.get('languages')),
                frameworks: lines(data.get('frameworks')),
                databases: lines(data.get('databases')),
                environment: lines(data.get('environment')),
                tools: lines(data.get('tools')),
                phases: lines(data.get('phases'))
            });
        });
    }

    function deleteSkill(side, group, name) {
        if (!window.confirm('「' + name + '」を削除しますか？')) {
            return;
        }
        sendJson('/admin/skills', 'DELETE', { side: side, group: group, name: name }).then(function (response) {
            if (response.ok) {
                window.location.reload();
                return;
            }
            window.alert('削除できませんでした');
        });
    }

    function deleteProject(id) {
        var project = findProject(id);
        var title = project ? (project.title || '') : '';
        if (!window.confirm('「' + title + '」を削除しますか？')) {
            return;
        }
        sendJson('/admin/projects/' + encodeURIComponent(id), 'DELETE', {}).then(function (response) {
            if (response.ok) {
                window.location.reload();
                return;
            }
            window.alert('削除できませんでした');
        });
    }

    function openEditor(title, fields, handler) {
        var body = document.getElementById('editor-fields');
        body.textContent = '';
        fields.forEach(function (node) {
            body.appendChild(node);
        });
        document.getElementById('editor-title').textContent = title;
        var error = document.getElementById('editor-error');
        error.hidden = true;
        error.textContent = '';
        document.getElementById('editor-submit').disabled = false;
        editorHandler = handler;
        document.getElementById('editor-modal').hidden = false;
        var first = body.querySelector('input, textarea, select');
        if (first) {
            first.focus();
        }
    }

    function closeEditor() {
        var editor = document.getElementById('editor-modal');
        if (editor) {
            editor.hidden = true;
        }
        editorHandler = null;
    }

    function saveEditor(url, method, body) {
        var error = document.getElementById('editor-error');
        var submit = document.getElementById('editor-submit');
        submit.disabled = true;
        error.hidden = true;
        sendJson(url, method, body).then(function (response) {
            if (response.ok) {
                window.location.reload();
                return;
            }
            return response.json().then(function (payload) {
                error.textContent = payload.message || '保存できませんでした';
                error.hidden = false;
                submit.disabled = false;
            }).catch(function () {
                error.textContent = '保存できませんでした';
                error.hidden = false;
                submit.disabled = false;
            });
        });
    }

    function findSkill(side, group, name) {
        var groups = ((skillSummary[side] || {}).groups || []);
        for (var i = 0; i < groups.length; i += 1) {
            if ((groups[i].label || '') !== group) {
                continue;
            }
            var skills = groups[i].skills || [];
            for (var j = 0; j < skills.length; j += 1) {
                if ((skills[j].name || '') === name) {
                    return skills[j];
                }
            }
        }
        return null;
    }

    function findProject(id) {
        var projects = career.projects || [];
        for (var i = 0; i < projects.length; i += 1) {
            if (projects[i].id === id) {
                return projects[i];
            }
        }
        return null;
    }

    function field(label, name, value, type) {
        var wrap = el('label', 'field');
        wrap.appendChild(el('span', null, label));
        var input = el(type === 'textarea' ? 'textarea' : 'input');
        if (type !== 'textarea') {
            input.type = type || 'text';
        }
        input.name = name;
        input.value = value || '';
        if (type === 'number') {
            input.min = '0';
            input.step = '1';
        }
        wrap.appendChild(input);
        return wrap;
    }

    function selectField(label, name, value, options) {
        var wrap = el('label', 'field');
        wrap.appendChild(el('span', null, label));
        var select = el('select');
        select.name = name;
        options.forEach(function (optionValue) {
            var option = el('option', null, optionValue);
            option.value = optionValue;
            select.appendChild(option);
        });
        var extra = el('option', null, '新しいグループ');
        extra.value = '__new__';
        select.appendChild(extra);
        if (value && options.indexOf(value) !== -1) {
            select.value = value;
        }
        wrap.appendChild(select);
        return wrap;
    }

    function lines(value) {
        return String(value || '').split(/\n/).map(function (line) {
            return line.trim();
        }).filter(Boolean);
    }

    function listText(items) {
        return (items || []).filter(Boolean).join('\n');
    }

    function periodParts(period) {
        var matched = String(period || '').match(/(\d{4})\/(\d{2})(?:\s*-\s*(\d{4})\/(\d{2}))?/);
        if (!matched) {
            return { start: '', end: '' };
        }
        return {
            start: matched[1] + '-' + matched[2],
            end: matched[3] ? matched[3] + '-' + matched[4] : ''
        };
    }

    function sortProjects(projects) {
        return projects.slice().sort(function (a, b) {
            var ap = formatPeriod(a.period);
            var bp = formatPeriod(b.period);
            if (ap.startKey !== bp.startKey) {
                return bp.startKey - ap.startKey;
            }
            return bp.endKey - ap.endKey;
        });
    }

    function formatPeriod(period) {
        var text = String(period || '');
        var matched = text.match(/(\d{4})\/(\d{2})\s*-\s*(\d{4})\/(\d{2})/);
        if (matched) {
            return {
                label: matched[1] + '/' + matched[2] + '〜' + matched[3] + '/' + matched[4],
                startKey: Number(matched[1]) * 100 + Number(matched[2]),
                endKey: Number(matched[3]) * 100 + Number(matched[4])
            };
        }
        matched = text.match(/(\d{4})\/(\d{2})/);
        if (matched) {
            return {
                label: matched[1] + '/' + matched[2] + '〜継続',
                startKey: Number(matched[1]) * 100 + Number(matched[2]),
                endKey: 0
            };
        }
        return { label: text, startKey: 0, endKey: 0 };
    }

    function joinList(items) {
        if (!items || !items.length) {
            return '';
        }
        return items.filter(Boolean).join('、');
    }

    function highlight(text, terms) {
        if (!terms.length) {
            return escapeHtml(text);
        }
        var pattern = new RegExp(terms.map(escapeReg).join('|'), 'gi');
        var html = '';
        var last = 0;
        text.replace(pattern, function (match, offset) {
            html += escapeHtml(text.slice(last, offset));
            html += '<mark>' + escapeHtml(match) + '</mark>';
            last = offset + match.length;
            return match;
        });
        html += escapeHtml(text.slice(last));
        return html;
    }

    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, function (char) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char];
        });
    }

    function escapeReg(value) {
        return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function addPair(dl, label, value) {
        dl.appendChild(el('dt', null, label));
        dl.appendChild(el('dd', null, value));
    }

    function addLink(dl, label, url) {
        dl.appendChild(el('dt', null, label));
        var dd = el('dd');
        var link = el('a', 'profile-link', url);
        link.href = url;
        link.target = '_blank';
        link.rel = 'noopener noreferrer';
        dd.appendChild(link);
        dl.appendChild(dd);
    }

    function row(cellTag, values) {
        var tr = el('tr');
        values.forEach(function (value) {
            tr.appendChild(el(cellTag, null, value));
        });
        return tr;
    }

    function cell(label, className, text, asHighlight) {
        var td = el('td', className);
        td.dataset.label = label;
        if (text) {
            td.appendChild(asHighlight ? hl('span', null, text) : document.createTextNode(text));
        }
        return td;
    }

    function hl(tag, className, text) {
        var node = el(tag, className ? className + ' js-hl' : 'js-hl', text);
        return node;
    }

    function button(label, className, id) {
        var node = el('button', className, label);
        node.type = 'button';
        if (id) {
            node.id = id;
        }
        return node;
    }

    function el(tag, className, text) {
        var node = document.createElement(tag);
        if (className) {
            node.className = className;
        }
        if (text != null && text !== '') {
            node.textContent = text;
        }
        return node;
    }
})();
