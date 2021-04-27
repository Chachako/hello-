import DP855

tcon = DP855.autoDetect()


print ("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!Look at this example!!!!!!!!!!!!!!!!!!!!!!!!!!")
print ("this reads the register 0x0f, 0xfe, which should return 0x55")
print (hex(tcon.rd(0x0f, 0xfe)))

length = 80

length_low = length & 0xff
length_high = (length>>8) & 0x03

spi_addr_init = 0x000000

multi_io_mode = 0 ## for Boot ROM, multi_io_mode should always be 0

if(multi_io_mode == 0):
    Read_CMD = 0x0b
    addr_dummy_num = 0x08
    addr_dummy_value = 0x00
elif(multi_io_mode == 2):
    # This part is not even needed, because read the above comment where it says ## for Boot ROM, multi_io_mode should always be 0
    pass
else:
    print("!!!Dual Mode(2-2-2)is not supported!!!")
    sys.exit(0)

addr_byte2 = (spi_addr_init>>16) & 0xff
addr_byte1 = (spi_addr_init>>8) & 0xff
addr_byte0 = spi_addr_init & 0xff

def spi_fifo0_init():

    ###########!!!!!This is what the application note says!!!!!########
    tcon.wr(0x10,0x3c,0x10,0x2c)
    tcon.wr(0x10,0x3c,0x11,0x08)
    tcon.wr(0x10,0x3c,0x14,0x01)
    tcon.wr(0x10,0x3c,0x15,0x01)

    ##########!!!!This is what you write!!!!!####, ignore the 0x10. that's already included in what I provided Same goes where it says ReadReg
    # tcon.wr(0x3c,0x10,0x2c)
    # tcon.wr(0x3c,0x11,0x08)
    # tcon.wr(0x3c,0x14,0x01)
    # tcon.wr(0x3c,0x15,0x01)
def spi_fifo1_init():
    tcon.wr(0x10,0x40,0x7c,0x2c)
    tcon.wr(0x10,0x40,0x7d,0x08)
    tcon.wr(0x10,0x40,0x80,0x01)
    tcon.wr(0x10,0x40,0x81,0x01)

def spi_fifo0_read():
    spi_fifo0_init()
    tcon.wr(0x10,0x3c,0x18,addr_dummy_num)
    tcon.wr(0x10,0x3c,0x19,0x81)
    tcon.wr(0x10,0x3c,0x1c,Read_CMD)
    tcon.wr(0x10,0x3c,0x1d,0x01)
    tcon.wr(0x10,0x3c,0x1e,0x03)
    tcon.wr(0x10,0x3c,0x20,length_low)
    tcon.wr(0x10,0x3c,0x21,length_high)
    tcon.wr(0x10,0x3c,0x22,addr_dummy_value)
    tcon.wr(0x10,0x3c,0x24,addr_byte0)
    tcon.wr(0x10,0x3c,0x25,addr_byte1)
    tcon.wr(0x10,0x3c,0x26,addr_byte2)
    tcon.wr(0x10,0x3c,0x28,0x01)
    while(1):
        SPI_STATUS_FIFO0=tcon.rd(0x10,0x40,0x70)
        SPI_BUSY_FLAG_FIFO0=SPI_STATUS_FIFO0&0x01
        print("SPI_BUSY_FLAG_FIFO0")
        print(SPI_BUSY_FLAG_FIFO0)
        if(SPI_BUSY_FLAG_FIFO0==0x01):
            print("!!!SPI BUS is busy!!!")
        else:
            print("!!!SPI transaction is Done!!!")
            break
    file_read="SPI_FIFO0_DATA_READ.txt"
    fp=open(file_read,"w")
    for i in range(length+1):
        FIFO0_ADDR=0x3c30+i
        FIFO0_DATA=tcon.rd(0x10,(FIFO0_ADDR>>8)&0xff,FIFO0_ADDR&0xff)
        fp.writelines(hex(FIFO0_DATA)[2:].zfill(2))
        fp.writelines("\n")
    fp.close()

def spi_fifo1_read():
    spi_fifo1_init()
    tcon.wr(0x10,0x40,0x84,addr_dummy_num)
    tcon.wr(0x10,0x40,0x85,0x81)
    tcon.wr(0x10,0x40,0x88,Read_CMD)
    tcon.wr(0x10,0x40,0x89,0x01)
    tcon.wr(0x10,0x40,0x8a,0x03)
    tcon.wr(0x10,0x40,0x8c,length_low)
    tcon.wr(0x10,0x40,0x8d,length_high)
    tcon.wr(0x10,0x40,0x8e,addr_dummy_value)
    tcon.wr(0x10,0x40,0x90,addr_byte0)
    tcon.wr(0x10,0x40,0x91,addr_byte1)
    tcon.wr(0x10,0x40,0x92,addr_byte2)
    tcon.wr(0x10,0x40,0x94,0x01)
    while(1):
        SPI_STATUS_FIFO1=tcon.rd(0x10,0x40,0x70)
        SPI_BUSY_FLAG_FIFO1=SPI_STATUS_FIFO1&0x01
        print("SPI_BUSY_FLAG_FIFO1")
        print(SPI_BUSY_FLAG_FIFO1)
        if(SPI_BUSY_FLAG_FIFO1==0x01):
            print("!!!SPI BUS is busy!!!")
        else:
            print("!!!SPI transaction is Done!!!")
            break
    file_read="SPI_FIFO1_DATA_READ.txt"
    fp=open(file_read,"w")
    for i in range(length+1):
        FIFO1_ADDR=0x4098+i
        FIFO1_DATA=tcon.rd(0x10,(FIFO1_ADDR>>8)&0xff,FIFO1_ADDR&0xff)
        fp.writelines(hex(FIFO1_DATA)[2:].zfill(2))
        fp.writelines("\n")
    fp.close()


tcon.wr(0x10,0x3c,0x05,0x0d)
while(1):
    SPI_MASTER_STATUS=tcon.rd(0x10,0x3c,0x0d)
    if(SPI_MASTER_STATUS==0X03) or (SPI_MASTER_STATUS==0x00):
        print("SPI Busy")
    else:
        print("SPI_MASTER_STATUS")
        print(SPI_MASTER_STATUS)
        break;
if SPI_MASTER_STATUS==0x01:
    fifo_sel=0
    print("SPI FIFO0 is locked")
elif(SPI_MASTER_STATUS==0x02):
    fifo_sel=1
    print("SPI FIFO1 is locked")
else:
    print("!!!SPI lock error!!!")
    # KeyInt()

if(fifo_sel):
    spi_fifo1_read()
else:
    spi_fifo0_read()

if(fifo_sel):
    tcon.wr(0x10,0x3c,0x05,0x01)
else:
    tcon.wr(0x10,0x3c,0x05,0x00)