import React from 'react'
import Cards from '../card/Cards';
import { Grid, Box } from '@mui/material';
import PropTypes from 'prop-types';

const CardList = ({ cardsData }) => {
  return (
    <Box sx={{ flexGrow: 1, padding: '20px' }}>
      <Grid container spacing={2}>
        {cardsData.map((card, index) => (
          <Grid item xs={12} sm={6} md={4} key={index}>
            <Cards
              title={card.title}
              description={card.description}
              imageUrl={card.imageUrl}
              buttonText={card.buttonText}
              onButtonClick={card.onButtonClick}
            />
          </Grid>
        ))}
      </Grid>
    </Box>
  );
}


CardList.propTypes = {
  cardsData: PropTypes.arrayOf(
    PropTypes.shape({
      title: PropTypes.string.isRequired,
      description: PropTypes.string.isRequired,
      imageUrl: PropTypes.string,
      buttonText: PropTypes.string,
      onButtonClick: PropTypes.func,
    })
  ).isRequired,
}

export default CardList