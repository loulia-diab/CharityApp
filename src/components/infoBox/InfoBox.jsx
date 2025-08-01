import React from 'react';
import {
  Box,
  Paper,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Typography,
  useTheme,
  Button
} from '@mui/material';
import { useNavigate } from 'react-router-dom';

const InfoBox = ({ 
  volunteers,
  columns = [
    { field: 'id', headerName: 'ID' },
    { field: 'firstName', headerName: 'الاسم الأول' },
    { field: 'lastName', headerName: 'الاسم الأخير' },
    { field: 'age', headerName: 'العمر' },
    { field: 'status', headerName: 'الحالة' }
  ],
  title = 'قائمة البيانات', // تغيير القيمة الافتراضية
  showTitle = true, // إضافة خاصية لإظهار/إخفاء العنوان
  titleVariant = 'h5', // التحكم بنوع العنوان
  showDetailsButton = true,
  detailsButtonText = 'details',
  colors = {
    headerBg: '#155e5d',
    headerText: '#ffffff',
    rowBg: '#d1bca0ff',
    evenRowBg: '#d2b48c',
    textColor: '#000000',
    buttonBg: '#32a3a1',
    buttonHover: '#155e5d',
    buttonText: '#ffffff',
    paperBg: '#ffffff',
    titleColor: '#155e5d'
  }
}) => {
  const theme = useTheme();
  const navigate = useNavigate();

  const handleDetailsClick = (volunteer) => {
    navigate(`/volunteer_details`, {
      state: { volunteerData: volunteer }
    });
  };

  return (
    <Box sx={{ 
      p: 3, 
      backgroundColor: '#f9f9f9',
      width: '100%'
    }}>
      {showTitle && (
        <Typography 
          variant={titleVariant} 
          gutterBottom 
          sx={{ 
            mb: 3, 
            fontWeight: 'bold',
            color: colors.titleColor
          }}
        >
          {title}
        </Typography>
      )}
      
      <TableContainer component={Paper} elevation={3} sx={{
        backgroundColor: colors.paperBg,
        overflowX: 'auto'
      }}>
        <Table sx={{ minWidth: 650 }} aria-label="data table">
          <TableHead sx={{ 
            backgroundColor: colors.headerBg,
            '& .MuiTableCell-root': {
              color: colors.headerText,
              fontWeight: 'bold',
              whiteSpace: 'nowrap'
            }
          }}>
            <TableRow>
              {columns.map((column) => (
                <TableCell key={column.field}>
                  {column.headerName}
                </TableCell>
              ))}
              {showDetailsButton && <TableCell>الإجراءات</TableCell>}
            </TableRow>
          </TableHead>

          <TableBody>
            {volunteers.map((volunteer, index) => (
              <TableRow 
                key={volunteer.id}
                sx={{ 
                  backgroundColor: index % 2 === 0 ? colors.rowBg : colors.evenRowBg,
                  '& .MuiTableCell-root': {
                    color: colors.textColor,
                    whiteSpace: 'nowrap'
                  }
                }}
              >
                {columns.map((column) => (
                  <TableCell key={`${volunteer.id}-${column.field}`}>
                    {volunteer[column.field] || 'undefined'}
                  </TableCell>
                ))}
                
                {showDetailsButton && (
                  <TableCell>
                    <Button 
                      variant="contained"
                      size="small"
                      onClick={() => handleDetailsClick(volunteer)}
                      sx={{
                        backgroundColor: colors.buttonBg,
                        color: colors.buttonText,
                        '&:hover': {
                          backgroundColor: colors.buttonHover,
                        },
                        fontWeight: 'bold',
                      }}
                    >
                      {detailsButtonText}
                    </Button>
                  </TableCell>
                )}
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </Box>
  );
};

export default InfoBox;