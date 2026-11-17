export default function attentionTicket(config = {}) {
    return {
        status: config.status || 'new',
        isUpdating: false,
        isRemoved: false,
        open: false,

        async transition(newStatus, url) {
            this.isUpdating = true;
            this.open = false;
            try {
                const res = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ status: newStatus })
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.status = newStatus;
                    if (newStatus === 'closed') {
                        setTimeout(() => { this.isRemoved = true; }, 400);
                    }
                } else {
                    alert(data.message || 'Error updating status');
                }
            } catch (e) {
                console.error(e);
                alert('Failed to connect to server. Please try again.');
            } finally {
                this.isUpdating = false;
            }
        },

        statusClasses() {
            const map = {
                'new': 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 border-blue-200/60 dark:border-blue-800/50',
                'in_progress': 'bg-brand-50 dark:bg-brand-950/40 text-brand-700 dark:text-brand-300 border-brand-200/60 dark:border-brand-800/50',
                'waiting': 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border-purple-200/60 dark:border-purple-800/50',
                'blocked': 'bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300 border-red-200/60 dark:border-red-800/50',
                'closed': 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200/60 dark:border-emerald-800/50'
            };
            return map[this.status] || 'bg-gray-50 dark:bg-slate-800 text-gray-700 dark:text-slate-300 border-gray-200/60 dark:border-slate-700';
        },

        statusDot() {
            const map = {
                'new': 'bg-blue-500',
                'in_progress': 'bg-brand-500',
                'waiting': 'bg-purple-500',
                'blocked': 'bg-red-500',
                'closed': 'bg-emerald-500'
            };
            return map[this.status] || 'bg-gray-400';
        },

        statusLabel() {
            return this.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        },

        allowedTransitions() {
            const transitions = {
                'new': ['in_progress', 'closed'],
                'in_progress': ['waiting', 'blocked', 'closed'],
                'waiting': ['in_progress', 'closed'],
                'blocked': ['in_progress', 'closed'],
                'closed': ['new', 'in_progress']
            };
            return transitions[this.status] || ['in_progress', 'closed'];
        }
    };
}
