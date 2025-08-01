import React from 'react'
import Sidebar from '../../components/sidebar/Sidebar'
import Navbar from '../../components/navbar/Navbar'
import Filter from '../../components/filters/Filter'
import './donators.scss'

const Donators = () => {
  return (
    <div className='donators'>
        <Sidebar />
       <div className="donatorsContainer">
        <Navbar />
        <Filter/>
      </div>
    </div>
  )
}

export default Donators