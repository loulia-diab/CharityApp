import 'package:flutter/material.dart';

class MyHomePage extends StatefulWidget {
  @override
  _MyHomePageState createState() => _MyHomePageState();
}

class _MyHomePageState extends State<MyHomePage> {
  int _selectedIndex = 0;

  final Color _selectedColor = Color(0xFFF5DEB3); // بيج
  final Color _unselectedColor = Colors.white;

  final List<Widget> _pages = [
    Center(child: Text('الصفحة الرئيسية', style: TextStyle(color: Colors.white))),
    Center(child: Text('صفحة التبرع', style: TextStyle(color: Colors.white))),
    Center(child: Text('صندوق الخير', style: TextStyle(color: Colors.white))),
    Center(child: Text('طلب المساعدة', style: TextStyle(color: Colors.white))),
  ];

  @override
  Widget build(BuildContext context) {
    return Directionality(
      textDirection: TextDirection.rtl,
      child: Scaffold(
        backgroundColor: Colors.black,
        body: _pages[_selectedIndex],
        bottomNavigationBar: BottomNavigationBar(
          currentIndex: _selectedIndex,
          onTap: (index) => setState(() => _selectedIndex = index),
          backgroundColor: Colors.black,
          type: BottomNavigationBarType.fixed,
          selectedItemColor: _selectedColor,
          unselectedItemColor: _unselectedColor,
          items: [
            BottomNavigationBarItem(
              icon: Icon(Icons.home),
              label: 'الرئيسية',
            ),
            BottomNavigationBarItem(
              icon: ImageIcon(
                AssetImage('assets/images/0.png'),
                color: _selectedIndex == 1 ? _selectedColor : _unselectedColor,
              ),
              label: 'التبرع',
            ),
            BottomNavigationBarItem(
              icon: Icon(Icons.favorite),
              label: 'الخير',
            ),
            BottomNavigationBarItem(
              icon: ImageIcon(
                AssetImage('assets/images/zakat.png'),
                color: _selectedIndex == 3 ? _selectedColor : _unselectedColor,
              ),
              label: 'مساعدة',
            ),
          ],
        ),
      ),
    );
  }
}
