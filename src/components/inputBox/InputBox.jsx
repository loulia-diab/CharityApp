import React from 'react';
import Box from '@mui/material/Box';
import TextField from '@mui/material/TextField';

const InputBox = ({
  width = '25ch',
  label = 'Outlined secondary',
  color = 'secondary',
  boxColor = '#ffffff',
  height = 'auto',
  multiline = true, // إضافة خاصية متعددة الأسطر
  minRows = 1, // عدد الأسطر الأدنى
  maxRows = 10, // عدد الأسطر الأقصى
}) => {
  return (
    <Box
      component="form"
      sx={{ 
        '& > :not(style)': { 
          m: 1,
          width: width,
          minHeight: height,
          backgroundColor: boxColor,
          borderRadius: '4px',
          padding: '16px',
          overflow: 'hidden', // لمنع التجاوز
        } 
      }}
      noValidate
      autoComplete="off"
    >
      <TextField
        label={label}
        color={color}
        focused
        multiline={multiline} // تفعيل وضع متعدد الأسطر
        minRows={minRows}
        maxRows={maxRows}
        sx={{
          backgroundColor: 'transparent',
          width: '100%',
          padding: '8px',
          '& .MuiInputLabel-root': {
            position: 'absolute',
            top: '10px', // تعديل موقع النص الإرشادي
            left: '8px',
            backgroundColor: boxColor, // لون خلفية النص الإرشادي
            padding: '0 4px',
          },
          '& .MuiInputBase-root': {
            height: multiline ? 'auto' : '100%', // تكييف الارتفاع
          },
        }}
        fullWidth
      />
    </Box>
  );
}

export default InputBox;