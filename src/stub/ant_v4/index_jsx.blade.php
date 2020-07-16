import React, { useState, useRef } from 'react';
import ProTable from '@ant-design/pro-table';
import { DownOutlined, PlusOutlined } from '@ant-design/icons';
import { Button, message } from 'antd';
import CreateForm from './components/CreateForm';
import UpdateForm from './components/UpdateForm';
import { showDeleteConfirm } from '@/xnz_components/confirm';
import { queryGII, updateGII, addGII, removeGII } from './service';

/**
* 删除节点
* @param row / row.id
*/
const handleDelete = async row => {
  const hide = message.loading('正在删除');
  if (!row) return true;
  try {
    let resp = await removeGII({
      {{primaryKeyOfFields($vars['fields_arr'])}}: row.{{primaryKeyOfFields($vars['fields_arr'])}},
    });
    hide();
    if(resp.code == 200){ message.success('删除成功，即将刷新'); return true; }
    else{ message.error(resp.message); return false; }
  } catch (error) {
    hide();
    message.error('删除请求异常，请重试');
    return false;
  }
};

const ThisTable = () => {
  const [createModalVisible, handleModalVisible] = useState(false);
  const [updateModalVisible, handleUpdateModalVisible] = useState(false);
  const [stepFormValues, setStepFormValues] = useState({});

  const actionRef = useRef();

  const columns = [
    @foreach($vars['fields_arr'] as $item){
      title: '{{fieldsFormLabel($item['Comment'])}}',
      dataIndex: '{{$item['Field']}}',
    },
    @endforeach{
      title: '操作',
      dataIndex: 'option',
      valueType: 'option',
      render: (text, row) => (
        <>
          <a
            className="handle-link"
            onClick={() => {
              showDeleteConfirm("删除ID:"+row.id , ()=>{
                if(handleDelete(row) && actionRef.current){
                    actionRef.current.reload();
                }
              })
            }}
          >
            删除
          </a>
          <a
            className="handle-link"
            onClick={() => {
              setStepFormValues(row);
              handleUpdateModalVisible(true);
            }}
          >
            修改
          </a>
        </>
      ),
    },
  ];


  return (
    <>
      <ProTable
        actionRef={actionRef}
        columns={columns}
        request={params =>queryGII(params)}
        rowKey="{{primaryKeyOfFields($vars['fields_arr'])}}"
        toolBarRender={(action, { selectedRows }) => [
            <Button type="primary" onClick={ () => {
                handleModalVisible(true);
            }}>
              <PlusOutlined />新建
            </Button>
          ]}
      />
      <CreateForm
        onSubmit={async fields => {
          const hide = message.loading('正在添加');
          try {
            const response = await addGII({
              ...fields,
            });
            hide();
            if(response.code == 200){
              message.success('添加成功');
              if (actionRef.current) {
                actionRef.current.reload(); // 刷新表格（列表）数据
              }
            }else{
              message.error(response.message);
            }
            handleModalVisible(false); // 隐藏新建弹窗
          } catch (error) {
            hide();
            message.error('添加失败，请检查错误！');
          }
        }}
        onCancel={() => handleModalVisible(false)}
        modalVisible={createModalVisible}
      />
      {stepFormValues && Object.keys(stepFormValues).length ? (
        <UpdateForm
          onSubmit={async fields => {
            const response = await updateGII(fields);
            if (response.code == 200) {
              message.success('修改成功');
              handleUpdateModalVisible(false);
              setStepFormValues({});
              if (actionRef.current) {
                actionRef.current.reload();
              }
            }else{
              message.error('更新失败!' + response.message);
            }
          }}
          onCancel={() => {
            handleUpdateModalVisible(false);
            setStepFormValues({});
          }}
          updateModalVisible={updateModalVisible}
          values={stepFormValues}
        />
      ) : null}
    </>
  );
};



export default ThisTable;
