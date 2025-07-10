import 'package:flutter/material.dart';

class BackgroundWrapper extends StatelessWidget {
  final Widget child;

  const BackgroundWrapper({Key? key, required this.child}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        Positioned.fill(
          child: Image.asset(
            'assets/images/5.png',
            fit: BoxFit.cover,
          ),
        ),
        child, // لاحظ: لم نضع Scaffold هنا
      ],
    );
  }
}
