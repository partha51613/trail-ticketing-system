export default function ticketFilter(config = {}) {
    return {
        tab: config.tab || 'active',
        search: config.search || '',
        project: config.project || '',
        status: config.status || '',
        priority: config.priority || '',
        assignee: config.assignee || '',
        sort: config.sort || 'id',
        direction: config.direction || 'desc',
        tableAnchor: config.tableAnchor || '#tickets-table',

        switchTab(targetTab) {
            const params = new URLSearchParams(window.location.search);
            if (targetTab === 'closed' || targetTab === 'deleted') {
                params.set('tab', targetTab);
                params.delete('status');
            } else {
                params.delete('tab');
            }
            params.delete('page');
            window.location.href = `${window.location.pathname}?${params.toString()}${this.tableAnchor}`;
        },

        applyFilters() {
            const params = new URLSearchParams(window.location.search);

            if (this.tab === 'closed' || this.tab === 'deleted') {
                params.set('tab', this.tab);
            } else {
                params.delete('tab');
            }

            if (this.search) {
                params.set('search', this.search);
            } else {
                params.delete('search');
            }

            if (this.project) {
                params.set('project_id', this.project);
            } else {
                params.delete('project_id');
            }

            if (this.status && this.tab === 'active') {
                params.set('status', this.status);
            } else {
                params.delete('status');
            }

            if (this.priority) {
                params.set('priority', this.priority);
            } else {
                params.delete('priority');
            }

            if (this.assignee) {
                params.set('assigned_to_id', this.assignee);
            } else {
                params.delete('assigned_to_id');
            }

            if (config.hasSort) {
                params.set('sort', this.sort);
                params.set('direction', this.direction);
            }

            params.delete('page');
            window.location.href = `${window.location.pathname}?${params.toString()}${this.tableAnchor}`;
        },

        sortBy(column) {
            if (this.sort === column) {
                this.direction = this.direction === 'asc' ? 'desc' : 'asc';
            } else {
                this.sort = column;
                this.direction = 'asc';
            }
            this.applyFilters();
        }
    };
}
