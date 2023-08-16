export default {
    props: ['index', 'field'],

    data() {
        return {
            lists: [{code: 0, name: '加载中...'}],
            loading: false
        }
    },

    methods: {
        // 加载数据列表
        loadLists() {
            if (this.loading) return;
            this.loading = true;

            Nova
                .request()
                .get(this.field.options)
                .then(response => {
                    this.handleLists(response.data.resources);
                });
        },

        // 筛选有效数据
        handleLists(lists) {
            let list = [];
            let pick = this.field.value;
            let _int = this.field.formatInt || false;
            lists.map(item => {
                let code = item.value || item.id;
                code = _int ? parseInt(code) : code.toString();
                if (pick.includes(code)) list.push(item);
            });
            this.formatLists(list);
        },

        // 格式化数据
        formatLists(lists) {
            if (lists.length === 0) {
                this.lists = [{code: 0, name: '暂无数据'}];
                return;
            }

            const _mix = this.field.nameWithCode || false;
            let array = [];
            lists.map(item => {
                let code = item.value || item.id
                let name = item.display || item.name;
                array.push({
                    code: code,
                    name: (_mix ? (code + ' - ') : '') + name
                });
            });
            this.lists = array;
        }
    }
}
