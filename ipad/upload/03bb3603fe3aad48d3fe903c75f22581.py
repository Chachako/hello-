import DP855

tcon = DP855.autoDetect()


print ("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!Look at this example!!!!!!!!!!!!!!!!!!!!!!!!!!")
print ("this reads the register 0x0f, 0xfe, which should return 0x55")
print (hex(tcon.rd(0x0f, 0xfe)))

length = 80

length_low = length & 0xff
length_high = (length>>8) & 0x03

spi_addr_init = 0x002000

multi_io_mode = 0 ## for Boot ROM, multi_io_mode should always be 0

if(multi_io_mode == 0):
    Read_CMD = 0x0b
    addr_dummy_num = 0x08
    addr_dummy_value = 0x00
elif(multi_io_mode == 2):
    # This part is not even needed, because read the above comment where it says ## for Boot ROM, multi_io_mode should always be 0
    pass
else:
    pass

addr_byte2 = (spi_addr_init>>16) & 0xff
addr_byte1 = (spi_addr_init>>8) & 0xff
addr_byte0 = spi_addr_init & 0xff

def spi_fifo0_init():

    ###########!!!!!This is what the application note says!!!!!########
    # tcon.wr(0x10,0x3c,0x10,0x2c)
    # tcon.wr(0x10,0x3c,0x11,0x08)
    # tcon.wr(0x10,0x3c,0x14,0x01)
    # tcon.wr(0x10,0x3c,0x15,0x01)

    ##########!!!!This is what you write!!!!!####, ignore the 0x10. that's already included in what I provided Same goes where it says ReadReg
    tcon.wr(0x3c,0x10,0x2c)
    tcon.wr(0x3c,0x11,0x08)
    tcon.wr(0x3c,0x14,0x01)
    tcon.wr(0x3c,0x15,0x01)
