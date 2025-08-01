import React from 'react';
import "./datatable.scss";
import { DataGrid } from '@mui/x-data-grid';
import { Button } from '@mui/material';

const columns = [
  { 
    field: 'id', 
    headerName: 'ID', 
    width: 70,
    headerClassName: 'header-bold',
  },
  { 
    field: 'firstName', 
    headerName: 'First name', 
    width: 130,
  },
  { 
    field: 'lastName', 
    headerName: 'Last name', 
    width: 130,
  },
  {
    field: 'age',
    headerName: 'Age',
    type: 'number',
    width: 90,
    align: 'left',
    headerAlign: 'left',
  },
  {
    field: 'fullName',
    headerName: 'Full name',
    description: 'This column has a value getter and is not sortable.',
    sortable: false,
    width: 160,
    valueGetter: (params) => {
      if (!params || !params.row) return '';
      return `${params.row.firstName || ''} ${params.row.lastName || ''}`.trim();
    },
  },
  {
    field: 'actions',
    headerName: 'Actions',
    width: 120,
    sortable: false,
    filterable: false,
    renderCell: (params) => (
      <Button 
        variant="contained" 
        size="small"
        onClick={() => handleDetailsClick(params.row)}
        sx={{
          backgroundColor: '#155e5d',
          color: 'white',
          '&:hover': {
            backgroundColor: '#388E3C',
          },
          fontWeight: 'bold',
          boxShadow: '0 2px 5px rgba(0,0,0,0.2)',
          textTransform: 'none',
          padding: '6px 12px',
        }}
      >
        Details
      </Button>
    ),
  },
];

const rows = [
  { id: 1, lastName: 'Snow', firstName: 'Jon', age: 35 },
  { id: 2, lastName: 'Lannister', firstName: 'Cersei', age: 42 },
  { id: 3, lastName: 'Lannister', firstName: 'Jaime', age: 45 },
  { id: 4, lastName: 'Stark', firstName: 'Arya', age: 16 },
  { id: 5, lastName: 'Targaryen', firstName: 'Daenerys', age: null },
  { id: 6, lastName: 'Melisandre', firstName: null, age: 150 },
  { id: 7, lastName: 'Clifford', firstName: 'Ferrara', age: 44 },
  { id: 8, lastName: 'Frances', firstName: 'Rossini', age: 36 },
  { id: 9, lastName: 'Roxie', firstName: 'Harvey', age: 65 },
];

const handleDetailsClick = (rowData) => {
  console.log('تفاصيل السطر:', rowData);
};

const Datatable = () => {
  return (
<div className='datatable' style={{ height: 400 }}>
      <DataGrid
        rows={rows}
        columns={columns}
        autoHeight
        hideFooter
        disableRowSelectionOnClick
        sx={{
          '& .header-bold': {
            fontWeight: 'bold',
          },
          '& .MuiDataGrid-virtualScroller': {
            overflow: 'visible',
          },
          // تخصيص لون صفوف الجدول
          '& .MuiDataGrid-row': {
            backgroundColor: '#f8f9fa', // لون خلفية الصف
            '&:nth-of-type(even)': {
              backgroundColor: '#d1bca0ff', // لون مختلف للصفوف الزوجية
            },'&:nth-of-type(odd)': {
              backgroundColor: '#dfd4c5ff', // لون مختلف للصفوف الزوجية
            },
            '&:hover': {
              backgroundColor: '#d2b48c', // لون عند التحويم
            },
          },
          // تخصيص لون الخلايا
          '& .MuiDataGrid-cell': {
            borderBottom: '1px solid #ced4da',
            color: '#495057', // لون النص
          },
          // تخصيص رأس الجدول
          '& .MuiDataGrid-columnHeaders': {
            backgroundColor: '#343a40', // لون خلفية الرأس
            color: 'black', // لون نص الرأس
          },
        }}
      />
    </div>
  );
};

export default Datatable;