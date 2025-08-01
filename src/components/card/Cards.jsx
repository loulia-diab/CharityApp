import React from 'react';
import PropTypes from 'prop-types';
import Card from '@mui/material/Card';
import CardContent from '@mui/material/CardContent';
import CardMedia from '@mui/material/CardMedia';
import Typography from '@mui/material/Typography';
import Button from '@mui/material/Button';
import CardActionArea from '@mui/material/CardActionArea';
import CardActions from '@mui/material/CardActions';
import { useNavigate } from 'react-router-dom';
import { ThemeProvider, createTheme } from '@mui/material/styles';

// إنشاء ثيم مخصص باللون الجديد
const theme = createTheme({
  palette: {
    primary: {
      main: '#155e5d',
      contrastText: '#fff',
    },
  },
});

const Cards = ({ 
  imageUrl = "../../../assets/person.jpg",//هي مو طالعة 
  imageHeight = 180,
  title,
  description,
  showActions = true,
  buttons = [
    { text: "Active", variant: "contained" },
    { text: "Archive", variant: "outlined" },
    { text: "Details", variant: "text" }
  ],
  onButtonClick
}) => {
  const navigate = useNavigate();

  const handleDefaultClick = (btnIndex) => {
    switch(btnIndex) {
      case 0: // زر التفاصيل
        navigate('/campaign_details');
        break;
      case 1: // زر التعديل
        console.log('تعديل العنصر');
        break;
      case 2: // زر الحذف
        console.log('حذف العنصر');
        break;
      default:
        break;
    }
  };

  return (
    <ThemeProvider theme={theme}>
      <Card sx={{ 
        width: 380,
        minHeight: 420,
        backgroundColor: '#d2b48c',
        display: 'flex',
        flexDirection: 'column',
        borderRadius: '12px',
        boxShadow: '0 4px 8px rgba(0,0,0,0.1)',
        transition: 'transform 0.3s, box-shadow 0.3s',
        '&:hover': {
          transform: 'translateY(-5px)',
          boxShadow: '0 6px 12px rgba(0,0,0,0.15)'
        }
      }}>
        <CardActionArea sx={{ flexGrow: 1 }}>
          <CardMedia
            component="img"
            height={imageHeight}
            image={imageUrl}
            alt={title}
            sx={{
              objectFit: 'cover',
              width: '100%',
              borderTopLeftRadius: '12px',
              borderTopRightRadius: '12px'
            }}
          />
          <CardContent sx={{ flexGrow: 1 }}>
            <Typography gutterBottom variant="h5" component="div" sx={{ fontWeight: 'bold' }}>
              {title}
            </Typography>
            <Typography variant="body2" sx={{ 
              color: 'text.secondary',
              lineHeight: '1.6',
              minHeight: '60px'
            }}>
              {description}
            </Typography>
          </CardContent>
        </CardActionArea>
        {showActions && (
          <CardActions sx={{ 
            padding: '16px',
            display: 'flex',
            justifyContent: 'space-between',
            gap: '8px'
          }}>
            {buttons.map((button, index) => (
              <Button 
                key={index}
                size="medium"
                variant={button.variant || "contained"}
                color="primary"
                onClick={() => onButtonClick ? onButtonClick(index) : handleDefaultClick(index)}
                sx={{
                  flex: 1,
                  fontWeight: 'bold',
                  '&:hover': {
                    transform: 'translateY(-2px)',
                    ...(button.variant === 'outlined' && {
                      backgroundColor: 'rgba(21, 94, 93, 0.08)',
                    }),
                    ...(button.variant === 'text' && {
                      backgroundColor: 'rgba(21, 94, 93, 0.08)',
                    }),
                  }
                }}
              >
                {button.text}
              </Button>
            ))}
          </CardActions>
        )}
      </Card>
    </ThemeProvider>
  );
};

Cards.propTypes = {
  title: PropTypes.string.isRequired,
  description: PropTypes.string.isRequired,
  imageUrl: PropTypes.string,
  imageHeight: PropTypes.number,
  showActions: PropTypes.bool,
  buttons: PropTypes.arrayOf(
    PropTypes.shape({
      text: PropTypes.string,
      variant: PropTypes.string
    })
  ),
  onButtonClick: PropTypes.func,
};

export default Cards;