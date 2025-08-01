import React from 'react';
import Button from '@mui/material/Button';

const Bottun = ({ 
  buttonText, 
  color = 'primary', 
  onClick, 
  active = false
}) => {
  return (
    <div>
      <Button 
        variant={active ? "contained" : "outlined"}
        color={color}
        onClick={onClick}
        sx={{
          borderRadius: '20px',
          padding: '8px 24px',
          fontSize: '13px',
          fontWeight: 'bold',
          backgroundColor: active ? '#d2b48c' : 'transparent',
          color: active ? 'black' : 'inherit',
          borderColor: active ? 'black' : 'black', 
          borderWidth: '2px', 
          '&:active': {
            backgroundColor: '#d2b48c',
            borderColor: 'black', 
            color: 'black'
          },
          '&:hover': {
            backgroundColor: active ? '#d2b48c' : '#f5f5f5',
            borderColor: 'black' 
          },
          transition: 'all 0.2s ease'
        }}
      >
        {buttonText}
      </Button>
    </div>
  );
};

export default Bottun;