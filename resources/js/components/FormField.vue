<template>
    <DefaultField
        :field="currentField"
        :errors="errors"
        :show-help-text="showHelpText"
        :full-width-content="fullWidthContent"
    >
        <template #field>
            <Splitter
	            style="height:calc(260px + 40px + 1.25rem)"
	            :class="errorClasses"
            >
                <SplitterPanel>
                    <Listbox
                        v-model="selected1"
                        :options="list1"
                        optionLabel="name"
                        optionValue="code"
                        filter
                        :virtualScrollerOptions="{ itemSize: 40 }"
                        :emptyMessage="field.messages.emptyMessage"
                        :filterPlaceholder="field.messages.filterPlaceholder"
                        :emptyFilterMessage="field.messages.emptyFilterMessage"
                        listStyle="height:260px"
                        @change="delColumn"
                        :pt="{
                            item: {
                                style: 'line-height: 40px'
                            }
                        }"
                    />
                </SplitterPanel>
                <SplitterPanel>
                    <Listbox
                        v-model="selected2"
                        :options="list2"
                        optionLabel="name"
                        optionValue="code"
                        filter
                        :virtualScrollerOptions="{ itemSize: 40 }"
                        :emptyMessage="field.messages.emptyMessage"
                        :filterPlaceholder="field.messages.filterPlaceholder"
                        :emptyFilterMessage="field.messages.emptyFilterMessage"
                        listStyle="height:260px"
                        @change="addColumn"
                        :pt="{
                            item: {
                                style: 'line-height: 40px'
                            }
                        }"
                    />
                </SplitterPanel>
            </Splitter>
        </template>
    </DefaultField>
</template>

<script>
import {DependentFormField, HandlesValidationErrors} from 'laravel-nova';

export default {
    mixins: [DependentFormField, HandlesValidationErrors],

    props: ['field', 'currentField'],

    data() {
        return {
            selected1: null,
            selected2: null,
            list1: [],
            list2: [],
            lists: [],
            check: {}
        }
    },

    methods: {
        // 删除选中
        delColumn() {
            if (this.selected1 === null ||
                this.selected1 === undefined) return;
            delete this.check[this.selected1];
            this.drawLists();
        },

        // 选中
        addColumn() {
            if (this.selected2 === null ||
                this.selected2 === undefined) return;
            this.check[this.selected2] = true;
            this.drawLists();
        },

        // 查询全部列表数据
	    _loadLists(request_url) {
            const method = this.currentField.method || 'get';
            if (method === 'post') {
                Nova
                    .request()
                    .post(request_url)
                    .then(response => {
                        this.handleData(response.data.resources);
                    });
            } else {
                Nova
                    .request()
                    .get(request_url)
                    .then(response => {
                        this.handleData(response.data.resources);
                    });
            }
        },

        // 处理选择清单
        handleData(lists) {
            const _mix = this.currentField.nameWithCode || false;
            this.lists = lists.map(function (item) {
				let code = item.value || item.id;
				let name = item.display || item.name;
                return {
                    code: code,
                    name: (_mix ? (code + ' - ') : '') + name,
                };
            });
            this.drawLists();
        },

        // 绘制lists
        drawLists() {
            let list1 = [];
            let list2 = [];
			this.lists.map((item) => {
				if (this.check[item.code] === undefined) list2.push(item);
				else list1.push(item);
			});
            this.list1 = list1;
            this.list2 = list2;
        },

        // 设置初始值
        setInitialValue() {
            console.log(this.field);
            console.log(this.currentField);
            const value = this.currentField.value;
            if (value !== null) {
				let init = {};
                value.map(function (item) {
	                init[item.id || item] = true;
                });
				this.check = init;
            }

            const options = this.currentField.options;
            if (typeof options === 'string') {
                this._loadLists(options);
            } else {
                this.handleData(options);
            }
        },

        // 填充表单数据
        fill(formData) {
            formData.append(this.fieldAttribute, JSON.stringify(Object.keys(this.check)));
        },
    },
}
</script>
