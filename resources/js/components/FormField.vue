<template>
    <DefaultField
        :field="field"
        :errors="errors"
        :show-help-text="showHelpText"
        :full-width-content="fullWidthContent"
    >
        <template #field>
            <Splitter style="height:calc(260px + 40px + 1.25rem)">
                <SplitterPanel>
                    <Listbox
                        v-model="selected1"
                        :options="list1"
                        optionLabel="name"
                        optionValue="code"
                        filter
                        :virtualScrollerOptions="{ itemSize: 40 }"
                        emptyMessage="暂无可选数据"
                        filterPlaceholder="输入关键词搜索"
                        emptyFilterMessage="暂无匹配数据"
                        listStyle="height:260px"
                        @change="delColumn"
                        :pt="{
                            item: {
                                class: 'cursor-pointer list-box-item',
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
                        emptyMessage="暂无可选数据"
                        filterPlaceholder="输入关键词搜索"
                        emptyFilterMessage="暂无匹配数据"
                        listStyle="height:260px"
                        @change="addColumn"
                        :pt="{
                            item: {
                                class: 'cursor-pointer list-box-item',
                                style: 'line-height: 40px'
                            }
                        }"
                    />
                </SplitterPanel>
            </Splitter>
        </template>
    </DefaultField>
</template>

<style>
.list-box-item:hover {
    color: #495057;
    background: #e9ecef;
}
</style>

<script>
import {FormField, HandlesValidationErrors} from 'laravel-nova';

export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

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

        // 查询所有列表数据
        loadLists() {
            Nova
                .request()
                .get(this.field.options)
                .then(response => {
                    const data = response.data;
                    let lists = [];
                    for (let i = 0; i < data.resources.length; i++) {
                        lists.push({
                            code: data.resources[i].value,
                            name: data.resources[i].display,
                        });
                    }
                    this.lists = this.handleData(lists);
                    this.drawLists();
                });
        },

        // 绘制lists
        drawLists() {
            let list1 = [];
            let list2 = [];
            let code_;
            for (let i = 0; i < this.lists.length; i++) {
                code_ = this.lists[i].code;
                if (this.check[code_] === undefined) {
                    list2.push(this.lists[i]);
                } else {
                    list1.push(this.lists[i]);
                }
            }
            this.list1 = list1;
            this.list2 = list2;
        },

        // 处理选择清单
        handleData(lists) {
            let data = [];
            let name;
            for (let i = 0; i < lists.length; i++) {
                if (typeof lists[i] !== 'object') continue;
                if (lists[i].code === undefined ||
                    lists[i].name === undefined) continue;
                name = this.field.nameWithCode === true ?
                    lists[i].code + ' - ' + lists[i].name :
                    lists[i].name;
                data.push({
                    code: lists[i].code,
                    name: name,
                });
            }
            return data;
        },

        // 设置初始值
        setInitialValue() {
            const value = this.field.value;
            let initCheck = {};
            value.map(function (item) {
                initCheck[item.id] = true;
            });
            this.check = initCheck;

            const options = this.field.options;
            if (typeof options === 'string') {
                this.loadLists();
            } else {
                this.lists = this.handleData(options);
                this.drawLists();
            }
        },

        // 填充表单数据
        fill(formData) {
            formData.append(this.fieldAttribute, JSON.stringify(Object.keys(this.check)));
        },
    },
}
</script>
