import React from 'react';
import './filter.scss';
import Bottun from '../Filter_Bottun/Bottun';
import Grid from '@mui/material/Grid';
import PropTypes from 'prop-types';

const Filter = ({ 
  buttons = [],
  activeFilter,
  spacing = 2,
  direction = 'row',
  justify = 'flex-start',
  buttonProps = {}
}) => {
  return (
    <div className='filter'>
      <Grid 
        container 
        spacing={spacing}
        direction={direction}
        justifyContent={justify}
      >
        {buttons.map((button) => (
          <Grid item key={button.value || button.text}>
            <Bottun
              buttonText={button.text}
              color={button.color || 'primary'}
              onClick={button.onClick}
              active={activeFilter === (button.value || button.text)}
              hoverColor={button.hoverColor}
              activeTextColor={button.activeTextColor}
              inactiveTextColor={button.inactiveTextColor}
              {...buttonProps}
              sx={{
                minWidth: '120px',
                ...button.sx,
                ...buttonProps.sx
              }}
            />
          </Grid>
        ))}
      </Grid>
    </div>
  );
};

Filter.propTypes = {
  buttons: PropTypes.arrayOf(
    PropTypes.shape({
      text: PropTypes.string.isRequired,
      value: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
      color: PropTypes.string,
      onClick: PropTypes.func,
      hoverColor: PropTypes.string,
      activeTextColor: PropTypes.string,
      inactiveTextColor: PropTypes.string,
      sx: PropTypes.object
    })
  ).isRequired,
  activeFilter: PropTypes.any,
  spacing: PropTypes.number,
  direction: PropTypes.oneOf(['row', 'column']),
  justify: PropTypes.string,
  buttonProps: PropTypes.object
};

export default Filter;