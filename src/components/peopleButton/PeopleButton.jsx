import React from 'react';
import Button from '@mui/material/Button';
import PersonIcon from '@mui/icons-material/Person';

const PeopleButton = ({ label, onClick, active = false }) => {
  return (
    <Button
      variant={active ? "contained" : "outlined"}
      onClick={onClick}
      endIcon={<PersonIcon />}
      sx={{
        background: active 
          ? 'linear-gradient(45deg, #155E5D 30%, #2CC4C2 90%)' 
          : 'transparent',
        color: active ? 'white' : '#155E5D',
        borderColor: '#155E5D',
        borderWidth: '2px',
        fontWeight: 'bold',
        textTransform: 'none',
        padding: '8px 16px',
        transition: 'all 0.3s ease',
        '&:hover': {
          background: active 
            ? 'linear-gradient(45deg, #0e4544 30%, #1a9e9c 90%)' 
            : 'linear-gradient(45deg, rgba(21, 94, 93, 0.1) 30%, rgba(44, 196, 194, 0.1) 90%)',
          borderColor: '#0e4544',
          color: active ? 'white' : '#0e4544'
        },
        '& .MuiButton-endIcon': {
          marginLeft: '6px',
          '& svg': {
            fontSize: '1.2rem'
          }
        }
      }}
    >
      {label}
    </Button>
  );
};

export default PeopleButton;